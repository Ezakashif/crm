<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Task;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\LeadListQueryService;
use App\Support\CrmValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
    public function __construct(
        protected LeadListQueryService $leadListQuery,
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Lead::class);

        $user = $request->user();

        $filters = $request->validate($this->leadListQuery->filterRules());

        $leads = $this->leadListQuery->query($user, $filters)
            ->limit(Lead::BOARD_CARD_LIMIT + 1)
            ->get();

        $boardTruncated = $leads->count() > Lead::BOARD_CARD_LIMIT;
        if ($boardTruncated) {
            $leads = $leads->take(Lead::BOARD_CARD_LIMIT);
        }

        $statuses = Lead::STATUSES;

        $users = $user->canViewAllLeads()
            ? User::active()->orderBy('name')->get()
            : collect();

        return view('leads.index', compact('leads', 'statuses', 'filters', 'users', 'boardTruncated'));
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

    public function store(StoreLeadRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();

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

        if ($lead->assigned_to) {
            $lead->loadMissing('assignee');

            ActivityLogger::log('lead.assigned', $lead, [
                'name' => $lead->name,
                'from_user_id' => null,
                'to_user_id' => $lead->assigned_to,
                'from' => null,
                'to' => $lead->assignee?->name ?? 'Unassigned',
            ]);
        }

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

        $lead->loadMissing('assignee');

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
            $rules['assigned_to'] = ['nullable', \App\Support\CrmValidation::existsInCompany('users', 'id', $user->company_id)];
        }

        $validated = $request->validate($rules);

        if (! $user->can('assign', $lead)) {
            unset($validated['assigned_to']);
        }

        $previousStatus = $lead->status;
        $previousAssigneeId = $lead->assigned_to;

        $lead->update($validated);
        $lead->loadMissing('assignee');

        ActivityLogger::log('lead.updated', $lead, [
            'name' => $lead->name,
        ]);

        if ($previousStatus !== $lead->status) {
            ActivityLogger::log('lead.status_changed', $lead, [
                'from' => $previousStatus,
                'to' => $lead->status,
            ]);
        }

        if (array_key_exists('assigned_to', $validated)
            && (int) $previousAssigneeId !== (int) $lead->assigned_to) {
            $fromUser = $previousAssigneeId
                ? User::query()->find($previousAssigneeId)
                : null;

            ActivityLogger::log('lead.assigned', $lead, [
                'name' => $lead->name,
                'from_user_id' => $previousAssigneeId,
                'to_user_id' => $lead->assigned_to,
                'from' => $fromUser?->name,
                'to' => $lead->assignee?->name ?? 'Unassigned',
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

        $existing = Customer::withTrashed()->where('source_lead_id', $lead->id)->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }

            if ($lead->status !== 'won') {
                $lead->status = 'won';
                $lead->save();
            }

            return redirect()
                ->route('customers.show', $existing)
                ->with('success', 'Lead was already converted to a customer.');
        }

        if ($lead->status === 'won') {
            return redirect()
                ->route('leads.show', $lead)
                ->with('success', 'Lead is already marked as won.');
        }

        $customer = DB::transaction(function () use ($lead) {
            $customer = Customer::create([
                'created_by' => auth()->id(),
                'source_lead_id' => $lead->id,
                'name' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'company_name' => $lead->company,
                'address' => null,
                'notes' => $lead->notes,
            ]);

            $lead->status = 'won';
            $lead->save();

            Task::query()
                ->where('lead_id', $lead->id)
                ->whereNull('customer_id')
                ->update(['customer_id' => $customer->id]);

            ActivityLogger::log('lead.converted', $lead, [
                'name' => $lead->name,
                'customer_id' => $customer->id,
            ]);

            ActivityLogger::log('customer.created', $customer, [
                'name' => $customer->name,
            ]);

            return $customer;
        });

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Lead converted to customer');
    }

    public function updateBoard(Request $request)
    {
        $request->validate([
            'lead_id' => ['required', \App\Support\CrmValidation::existsInCompany('leads', 'id', $request->user()->company_id)],
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
