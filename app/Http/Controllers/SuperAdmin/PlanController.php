<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\PlanRequest;
use App\Models\Plan;
use App\Services\ActivityLogger;
use App\Services\SuperAdmin\PlanManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:active,inactive'],
            'type' => ['nullable', 'in:free,paid,featured,public,hidden'],
        ]);

        $plans = Plan::query()
            ->withCount(['features', 'limits', 'companies'])
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%")))
            ->when(($filters['status'] ?? null) === 'active', fn ($query) => $query->where('is_active', true))
            ->when(($filters['status'] ?? null) === 'inactive', fn ($query) => $query->where('is_active', false))
            ->when(($filters['type'] ?? null) === 'free', fn ($query) => $query->where('is_free', true))
            ->when(($filters['type'] ?? null) === 'paid', fn ($query) => $query->where('is_free', false))
            ->when(($filters['type'] ?? null) === 'featured', fn ($query) => $query->where('is_featured', true))
            ->when(($filters['type'] ?? null) === 'public', fn ($query) => $query->where('is_public', true))
            ->when(($filters['type'] ?? null) === 'hidden', fn ($query) => $query->where('is_public', false))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('superadmin.plans.index', compact('plans', 'filters'));
    }

    public function create(): View
    {
        return view('superadmin.plans.create', ['plan' => new Plan]);
    }

    public function store(PlanRequest $request, PlanManagementService $plans): RedirectResponse
    {
        $plan = $plans->create($request->validated(), $request->user()->id);
        ActivityLogger::log('plan.created', $plan, ['name' => $plan->name, 'slug' => $plan->slug]);

        return redirect()->route('superadmin.plans.edit', $plan)->with('success', 'Subscription plan created.');
    }

    public function edit(Plan $plan): View
    {
        return view('superadmin.plans.edit', ['plan' => $plan->load(['features', 'limits'])]);
    }

    public function update(PlanRequest $request, Plan $plan, PlanManagementService $plans): RedirectResponse
    {
        $plan = $plans->update($plan, $request->validated(), $request->user()->id);
        ActivityLogger::log('plan.updated', $plan, ['name' => $plan->name, 'slug' => $plan->slug]);

        return back()->with('success', 'Subscription plan updated.');
    }

    public function duplicate(Plan $plan, PlanManagementService $plans): RedirectResponse
    {
        $copy = $plans->duplicate($plan->load(['features', 'limits']), request()->user()->id);
        ActivityLogger::log('plan.duplicated', $copy, ['name' => $copy->name, 'source_plan_id' => $plan->id]);

        return redirect()->route('superadmin.plans.edit', $copy)->with('success', 'Plan duplicated as a private draft.');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        if ($plan->companies()->exists()) {
            return back()->withErrors(['plan' => 'Reassign companies before deleting this plan.']);
        }

        if ($plan->is_default) {
            return back()->withErrors(['plan' => 'The default plan cannot be deleted.']);
        }

        $plan->delete();
        ActivityLogger::log('plan.deleted', $plan, ['name' => $plan->name, 'slug' => $plan->slug]);

        return redirect()->route('superadmin.plans.index')->with('success', 'Subscription plan deleted.');
    }
}
