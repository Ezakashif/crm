<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Task;
use App\Services\ActivityLogger;
use App\Services\CustomerListQueryService;
use App\Services\CustomerTimelineService;
use App\Support\CrmValidation;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        protected CustomerListQueryService $customerListQuery,
        protected CustomerTimelineService $customerTimeline,
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Customer::class);

        $filters = $request->validate($this->customerListQuery->filterRules());

        $customers = $this->customerListQuery
            ->query($filters)
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', compact('customers', 'filters'));
    }

    public function create()
    {
        $this->authorize('create', Customer::class);

        return view('customers.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Customer::class);

        $request->validate(CrmValidation::customerStoreRules());

        $customer = Customer::create([
            'created_by' => auth()->id(),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'notes' => $request->notes,
            'company_name' => $request->company_name,
            'status' => 'active',
        ]);

        ActivityLogger::log('customer.created', $customer, [
            'name' => $customer->name,
        ]);

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully');
    }

    public function show(Customer $customer)
    {
        $this->authorize('view', $customer);

        $customer->load(['creator', 'sourceLead.assignee']);

        $tasks = Task::query()
            ->where(function ($query) use ($customer) {
                $query->where('customer_id', $customer->id);

                if ($customer->source_lead_id) {
                    $query->orWhere('lead_id', $customer->source_lead_id);
                }
            })
            ->with('assignee:id,name')
            ->latest('id')
            ->limit(10)
            ->get();

        $timeline = $this->customerTimeline->forCustomer($customer, request()->user());

        return view('customers.show', compact('customer', 'tasks', 'timeline'));
    }

    public function edit(Customer $customer)
    {
        $this->authorize('update', $customer);

        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $this->authorize('update', $customer);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $customer->update($request->all());

        ActivityLogger::log('customer.updated', $customer, [
            'name' => $customer->name,
        ]);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully');
    }

    public function destroy(Customer $customer)
    {
        $this->authorize('delete', $customer);

        ActivityLogger::log('customer.deleted', $customer, [
            'name' => $customer->name,
        ]);

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully');
    }
}
