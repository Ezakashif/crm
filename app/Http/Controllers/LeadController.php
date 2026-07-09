<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Lead::class);

        $filters = $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:new,contacted,qualified,proposal_sent,won,lost',
            'assigned_to' => 'nullable|string',
            'source' => 'nullable|in:'.implode(',', Lead::SOURCES),
        ]);

        $leads = Lead::with('assignee')
            ->search($filters['search'] ?? null)
            ->status($filters['status'] ?? null)
            ->assignedTo($filters['assigned_to'] ?? null)
            ->source($filters['source'] ?? null)
            ->orderBy('status')
            ->orderBy('sort_order')
            ->get();

        $statuses = Lead::STATUSES;

        $users = User::active()->orderBy('name')->get();

        return view('leads.index', compact('leads', 'statuses', 'filters', 'users'));
    }

    public function create()
    {
        $this->authorize('create', Lead::class);

        $users = User::active()->orderBy('name')->get();

        return view('leads.create', compact('users'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Lead::class);

        $request->validate([
            'name' => 'required',
            'status' => 'required',
        ]);

        $sortOrder = Lead::where('status', 'new')->max('sort_order') + 1;

        $lead = Lead::create([
            'created_by' => auth()->id(),
            'assigned_to' => $request->assigned_to,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company' => $request->company,
            'source' => $request->source,
            'status' => 'new',
            'sort_order' => $sortOrder,
            'estimated_value' => $request->estimated_value,
            'notes' => $request->notes,
            'follow_up_date' => $request->follow_up_date,
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

        $users = User::active()->orderBy('name')->get();

        return view('leads.edit', compact('lead', 'users'));
    }

    public function update(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        $lead->update($request->all());

        ActivityLogger::log('lead.updated', $lead, [
            'name' => $lead->name,
        ]);

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
