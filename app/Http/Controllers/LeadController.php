<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Customer;

class LeadController extends Controller
{
     public function index()
    {
    $leads = Lead::with('assignee')
    ->orderBy('status')
    ->orderBy('sort_order')
    ->get();

    $statuses = [
    'new' => 'New',
    'contacted' => 'Contacted',
    'qualified' => 'Qualified',
    'proposal_sent' => 'Proposal Sent',
    'won' => 'Won',
    'lost' => 'Lost',
];

return view('leads.index', compact('leads', 'statuses'));
    }

    public function create()
    {
        return view('leads.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
        ]);

       $sortOrder = Lead::where('status', 'new')->max('sort_order') + 1;

        Lead::create([
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

        return redirect()->route('leads.index')->with('success', 'Lead created');
    }

    public function edit(Lead $lead)
    {
        return view('leads.edit', compact('lead'));
    }

    public function update(Request $request, Lead $lead)
    {
        $lead->update($request->all());

        return redirect()->route('leads.index')->with('success', 'Lead updated');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return redirect()->route('leads.index')->with('success', 'Lead deleted');
    }

    //Implement the convertToCustomer method to convert a lead to a customer

    public function convertToCustomer(Lead $lead)
{
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

    $lead->update([
        'status' => $request->status,
        'sort_order' => $request->sort_order,
    ]);

    return response()->json([
        'success' => true,
    ]);
}
}
