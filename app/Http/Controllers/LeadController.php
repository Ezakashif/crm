<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Support\CrmValidation;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Lead::class);

        $user = $request->user();

        $filters = $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:new,contacted,qualified,proposal_sent,won,lost',
            'assigned_to' => 'nullable|string',
            'source' => 'nullable|in:'.implode(',', Lead::SOURCES),
        ]);

        $leads = Lead::visibleTo($user)
            ->with('assignee')
            ->search($filters['search'] ?? null)
            ->status($filters['status'] ?? null)
            ->when($user->canViewAllLeads(), fn ($query) => $query->assignedTo($filters['assigned_to'] ?? null))
            ->source($filters['source'] ?? null)
            ->orderBy('status')
            ->orderBy('sort_order')
            ->get();

        $statuses = Lead::STATUSES;

        $users = $user->canViewAllLeads()
            ? User::active()->orderBy('name')->get()
            : collect();

        return view('leads.index', compact('leads', 'statuses', 'filters', 'users'));
    }

    public function create()
    {
        $this->authorize('create', Lead::class);

        $user = auth()->user();

        $users = $user->canAssignLeads()
            ? User::active()->orderBy('name')->get()
            : collect();

        return view('leads.create', compact('users'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Lead::class);

        $user = $request->user();

        $rules = CrmValidation::leadStoreRules($user);

        $validated = $request->validate($rules);

        $assignedTo = $user->canAssignLeads()
            ? ($validated['assigned_to'] ?? null)
            : $user->id;

        $sortOrder = Lead::where('status', 'new')->max('sort_order') + 1;

        $lead = Lead::create([
            'created_by' => $user->id,
            'assigned_to' => $assignedTo,
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'company' => $validated['company'] ?? null,
            'source' => $validated['source'] ?? null,
            'status' => 'new',
            'sort_order' => $sortOrder,
            'estimated_value' => $validated['estimated_value'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'follow_up_date' => $validated['follow_up_date'] ?? null,
        ]);

        ActivityLogger::log('lead.created', $lead, [
            'name' => $lead->name,
        ]);

        return redirect()->route('leads.show', $lead)->with('success', 'Lead created');
    }

    public function show(Lead $lead)
    {
        $this->authorize('view', $lead);

        $lead->load(['assignee', 'creator', 'activities.user', 'tasks.assignee']);

        $activityTypes = collect(LeadActivity::TYPE_LABELS)
            ->except('status_change')
            ->all();

        return view('leads.show', compact('lead', 'activityTypes'));
    }

    public function edit(Lead $lead)
    {
        $this->authorize('update', $lead);

        $user = auth()->user();

        $users = ($user->canViewAllLeads() || $user->can('assign', $lead))
            ? User::active()->orderBy('name')->get()
            : collect();

        return view('leads.edit', compact('lead', 'users'));
    }

    public function update(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        $user = $request->user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'source' => 'nullable|in:'.implode(',', Lead::SOURCES),
            'status' => 'required|in:'.implode(',', array_keys(Lead::STATUSES)),
            'estimated_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
        ];

        if ($user->can('assign', $lead)) {
            $rules['assigned_to'] = 'nullable|exists:users,id';
        }

        $validated = $request->validate($rules);

        if (! $user->can('assign', $lead)) {
            unset($validated['assigned_to']);
        }

        $previousStatus = $lead->status;

        $lead->update($validated);

        ActivityLogger::log('lead.updated', $lead, [
            'name' => $lead->name,
        ]);

        if ($previousStatus !== $lead->status) {
            ActivityLogger::log('lead.status_changed', $lead, [
                'from' => $previousStatus,
                'to' => $lead->status,
            ]);
        }

        return redirect()->route('leads.show', $lead)->with('success', 'Lead updated');
    }

    public function destroy(Lead $lead)
    {
        $this->authorize('delete', $lead);

        ActivityLogger::log('lead.deleted', $lead, [
            'name' => $lead->name,
        ]);

        $lead->delete();

        return redirect()->route('leads.index')->with('success', 'Lead deleted');
    }

    public function convertToCustomer(Lead $lead)
    {
        $this->authorize('convert', $lead);

        $customer = Customer::create([
            'created_by' => auth()->id(),
            'name' => $lead->name,
            'email' => $lead->email,
            'phone' => $lead->phone,
            'company_name' => $lead->company,
            'address' => null,
            'notes' => $lead->notes,
        ]);

        $lead->status = 'won';
        $lead->save();

        ActivityLogger::log('lead.converted', $lead, [
            'name' => $lead->name,
        ]);

        ActivityLogger::log('customer.created', $customer, [
            'name' => $customer->name,
        ]);

        return redirect()->route('customers.edit', $customer->id)
            ->with('success', 'Lead converted to customer');
    }

    public function updateBoard(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'status' => 'required|in:new,contacted,qualified,proposal_sent,won,lost',
            'sort_order' => 'required|integer|min:0',
        ]);

        $lead = Lead::findOrFail($request->lead_id);

        $this->authorize('update', $lead);

        $previousStatus = $lead->status;

        $lead->update([
            'status' => $request->status,
            'sort_order' => $request->sort_order,
        ]);

        if ($previousStatus !== $request->status) {
            ActivityLogger::log('lead.status_changed', $lead, [
                'from' => $previousStatus,
                'to' => $request->status,
            ]);

            LeadActivity::log(
                $lead,
                'status_change',
                sprintf(
                    'Status changed from %s to %s',
                    Lead::STATUSES[$previousStatus] ?? $previousStatus,
                    Lead::STATUSES[$request->status] ?? $request->status,
                ),
            );
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
