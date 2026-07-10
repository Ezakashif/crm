<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;

class ReportService
{
    /**
     * Build the full reports payload for the authenticated user.
     *
     * @param  array{
     *     date_from: string,
     *     date_to: string,
     *     employee_id: int|null,
     *     source: string|null,
     *     status: string|null
     * }  $filters
     * @return array<string, mixed>
     */
    public function forUser(User $user, array $filters): array
    {
        $canViewLeads = $user->hasPermission('view.leads');
        $canViewTasks = $user->hasPermission('view.tasks');
        $canViewCustomers = $user->hasPermission('view.customers');
        $canExport = $user->hasPermission('export.reports');
        $canFilterEmployees = $user->canViewAllLeads() || $user->canViewAllTasks();

        $dateFrom = Carbon::parse($filters['date_from'])->startOfDay();
        $dateTo = Carbon::parse($filters['date_to'])->endOfDay();

        $leadQuery = $canViewLeads
            ? $this->filteredLeadsQuery($user, $filters, $dateFrom, $dateTo)
            : null;

        $taskQuery = $canViewTasks
            ? $this->filteredTasksQuery($user, $filters, $dateFrom, $dateTo)
            : null;

        $customerQuery = $canViewCustomers
            ? $this->filteredCustomersQuery($dateFrom, $dateTo)
            : null;

        return [
            'filters' => $filters,
            'canViewLeads' => $canViewLeads,
            'canViewTasks' => $canViewTasks,
            'canViewCustomers' => $canViewCustomers,
            'canExport' => $canExport,
            'canFilterEmployees' => $canFilterEmployees,
            'employees' => $canFilterEmployees
                ? User::active()->orderBy('name')->get(['id', 'name'])
                : collect(),
            'leadStatuses' => Lead::STATUSES,
            'leadSources' => Lead::SOURCES,
            'leads' => $canViewLeads ? $this->leadReport($leadQuery) : null,
            'customers' => $canViewCustomers ? $this->customerReport($customerQuery, $leadQuery) : null,
            'tasks' => $canViewTasks ? $this->taskReport($taskQuery) : null,
            'performance' => $canViewLeads ? $this->performanceReport($user, $filters, $dateFrom, $dateTo) : null,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function filteredLeadsQuery(User $user, array $filters, Carbon $dateFrom, Carbon $dateTo): Builder
    {
        $query = Lead::visibleTo($user)
            ->whereBetween('leads.created_at', [$dateFrom, $dateTo]);

        $employeeId = $this->resolveEmployeeFilter($user, $filters['employee_id'] ?? null, 'leads');

        if ($employeeId !== null) {
            $query->where('leads.assigned_to', $employeeId);
        }

        if (filled($filters['source'] ?? null)) {
            $query->where('leads.source', $filters['source']);
        }

        if (filled($filters['status'] ?? null)) {
            $query->where('leads.status', $filters['status']);
        }

        return $query;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function filteredTasksQuery(User $user, array $filters, Carbon $dateFrom, Carbon $dateTo): Builder
    {
        $query = Task::visibleTo($user)
            ->whereBetween('tasks.created_at', [$dateFrom, $dateTo]);

        $employeeId = $this->resolveEmployeeFilter($user, $filters['employee_id'] ?? null, 'tasks');

        if ($employeeId !== null) {
            $query->where('tasks.assigned_to', $employeeId);
        }

        return $query;
    }

    public function filteredCustomersQuery(Carbon $dateFrom, Carbon $dateTo): Builder
    {
        return Customer::query()->whereBetween('created_at', [$dateFrom, $dateTo]);
    }

    /**
     * Sales users cannot filter by other employees.
     */
    protected function resolveEmployeeFilter(User $user, ?int $employeeId, string $module): ?int
    {
        $canViewAll = $module === 'leads'
            ? $user->canViewAllLeads()
            : $user->canViewAllTasks();

        if (! $canViewAll) {
            return null;
        }

        return $employeeId;
    }

    /**
     * @return array<string, mixed>
     */
    protected function leadReport(Builder $leadQuery): array
    {
        $statusCounts = (clone $leadQuery)
            ->selectRaw('leads.status, COUNT(*) as aggregate')
            ->groupBy('leads.status')
            ->pluck('aggregate', 'status');

        $byStatus = [];
        foreach (Lead::STATUSES as $key => $label) {
            $byStatus[] = [
                'status' => $key,
                'label' => $label,
                'count' => (int) ($statusCounts[$key] ?? 0),
            ];
        }

        $byAssignee = (clone $leadQuery)
            ->leftJoin('users', 'leads.assigned_to', '=', 'users.id')
            ->selectRaw('leads.assigned_to, COALESCE(users.name, ?) as employee_name, COUNT(*) as aggregate', ['Unassigned'])
            ->groupBy('leads.assigned_to', 'users.name')
            ->orderByDesc('aggregate')
            ->get()
            ->map(fn ($row) => [
                'employee_id' => $row->assigned_to,
                'employee' => $row->employee_name,
                'count' => (int) $row->aggregate,
            ])
            ->all();

        $byDate = (clone $leadQuery)
            ->selectRaw($this->dateExpression('leads.created_at').' as day_key, COUNT(*) as aggregate')
            ->groupBy('day_key')
            ->orderBy('day_key')
            ->pluck('aggregate', 'day_key')
            ->map(fn ($count) => (int) $count)
            ->all();

        return [
            'total' => (int) $statusCounts->sum(),
            'by_status' => $byStatus,
            'by_status_chart' => [
                'labels' => array_column($byStatus, 'label'),
                'data' => array_column($byStatus, 'count'),
            ],
            'by_source_chart' => $this->leadSourceDistribution(clone $leadQuery),
            'by_assignee' => $byAssignee,
            'by_date' => $byDate,
            'monthly_growth' => $this->monthlyLeadGrowth(clone $leadQuery),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function customerReport(?Builder $customerQuery, ?Builder $leadQuery): array
    {
        $total = $customerQuery ? (clone $customerQuery)->count() : 0;

        $newThisMonth = Customer::query()
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        // Converted customers = won leads within the filtered lead set / date range.
        $converted = $leadQuery
            ? (clone $leadQuery)->where('status', 'won')->count()
            : 0;

        $byDate = $customerQuery
            ? (clone $customerQuery)
                ->selectRaw($this->dateExpression('created_at').' as day_key, COUNT(*) as aggregate')
                ->groupBy('day_key')
                ->orderBy('day_key')
                ->get()
                ->map(fn ($row) => [
                    'date' => $row->day_key,
                    'count' => (int) $row->aggregate,
                ])
                ->all()
            : [];

        return [
            'total' => $total,
            'new_this_month' => $newThisMonth,
            'converted' => $converted,
            'by_date' => $byDate,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function taskReport(Builder $taskQuery): array
    {
        $statusCounts = (clone $taskQuery)
            ->selectRaw('tasks.status, COUNT(*) as aggregate')
            ->groupBy('tasks.status')
            ->pluck('aggregate', 'status');

        $pending = (int) ($statusCounts['pending'] ?? 0);
        $completed = (int) ($statusCounts['completed'] ?? 0);
        $overdue = $this->overdueTasksQuery(clone $taskQuery)->count();

        $byEmployee = (clone $taskQuery)
            ->leftJoin('users', 'tasks.assigned_to', '=', 'users.id')
            ->selectRaw('tasks.assigned_to, COALESCE(users.name, ?) as employee_name, COUNT(*) as aggregate', ['Unassigned'])
            ->groupBy('tasks.assigned_to', 'users.name')
            ->orderByDesc('aggregate')
            ->get()
            ->map(fn ($row) => [
                'employee_id' => $row->assigned_to,
                'employee' => $row->employee_name,
                'count' => (int) $row->aggregate,
            ])
            ->all();

        $byDate = (clone $taskQuery)
            ->selectRaw($this->dateExpression('tasks.created_at').' as day_key, COUNT(*) as aggregate')
            ->groupBy('day_key')
            ->orderBy('day_key')
            ->get()
            ->map(fn ($row) => [
                'date' => $row->day_key,
                'count' => (int) $row->aggregate,
            ])
            ->all();

        return [
            'pending' => $pending,
            'completed' => $completed,
            'overdue' => $overdue,
            'in_progress' => (int) ($statusCounts['in_progress'] ?? 0),
            'cancelled' => (int) ($statusCounts['cancelled'] ?? 0),
            'total' => (int) $statusCounts->sum(),
            'by_employee' => $byEmployee,
            'by_date' => $byDate,
            'by_status_chart' => [
                'labels' => ['Pending', 'In Progress', 'Completed', 'Cancelled'],
                'data' => [
                    $pending,
                    (int) ($statusCounts['in_progress'] ?? 0),
                    $completed,
                    (int) ($statusCounts['cancelled'] ?? 0),
                ],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    protected function performanceReport(User $user, array $filters, Carbon $dateFrom, Carbon $dateTo): array
    {
        $base = Lead::visibleTo($user)
            ->whereBetween('leads.created_at', [$dateFrom, $dateTo]);

        $employeeId = $this->resolveEmployeeFilter($user, $filters['employee_id'] ?? null, 'leads');

        if ($employeeId !== null) {
            $base->where('leads.assigned_to', $employeeId);
        }

        if (filled($filters['source'] ?? null)) {
            $base->where('leads.source', $filters['source']);
        }

        // Performance ignores lead status filter so conversion stays meaningful.
        $rows = (clone $base)
            ->leftJoin('users', 'leads.assigned_to', '=', 'users.id')
            ->selectRaw('
                leads.assigned_to,
                COALESCE(users.name, ?) as employee_name,
                COUNT(*) as assigned_count,
                SUM(CASE WHEN leads.status = ? THEN 1 ELSE 0 END) as converted_count
            ', ['Unassigned', 'won'])
            ->groupBy('leads.assigned_to', 'users.name')
            ->orderByDesc('converted_count')
            ->orderByDesc('assigned_count')
            ->get();

        $employees = $rows->map(function ($row) {
            $assigned = (int) $row->assigned_count;
            $converted = (int) $row->converted_count;

            return [
                'employee_id' => $row->assigned_to,
                'employee' => $row->employee_name,
                'assigned' => $assigned,
                'converted' => $converted,
                'conversion_rate' => $assigned > 0
                    ? round(($converted / $assigned) * 100, 1)
                    : 0.0,
            ];
        });

        $totalAssigned = (int) $employees->sum('assigned');
        $totalConverted = (int) $employees->sum('converted');

        return [
            'leads_assigned' => $totalAssigned,
            'leads_converted' => $totalConverted,
            'conversion_rate' => $totalAssigned > 0
                ? round(($totalConverted / $totalAssigned) * 100, 1)
                : 0.0,
            'by_employee' => $employees->values()->all(),
            'top_performers' => $employees
                ->sortByDesc(fn (array $row) => [$row['converted'], $row['conversion_rate'], $row['assigned']])
                ->take(5)
                ->values()
                ->all(),
        ];
    }

    protected function overdueTasksQuery(Builder $query): Builder
    {
        return $query
            ->whereNotIn('tasks.status', ['completed', 'cancelled'])
            ->whereNotNull('tasks.due_date')
            ->whereDate('tasks.due_date', '<', today());
    }

    /**
     * @return array{labels: list<string>, data: list<int>}
     */
    protected function monthlyLeadGrowth(Builder $leadQuery): array
    {
        $start = now()->startOfMonth()->subMonths(5);
        $end = now()->endOfMonth();

        $monthExpression = $this->monthExpression('leads.created_at');

        $counts = (clone $leadQuery)
            ->where('leads.created_at', '>=', $start)
            ->selectRaw("{$monthExpression} as month_key, COUNT(*) as aggregate")
            ->groupBy('month_key')
            ->pluck('aggregate', 'month_key');

        $labels = [];
        $data = [];

        foreach (CarbonPeriod::create($start, '1 month', $end) as $month) {
            /** @var Carbon $month */
            $key = $month->format('Y-m');
            $labels[] = $month->format('M Y');
            $data[] = (int) ($counts[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * @return array{labels: list<string>, data: list<int>}
     */
    protected function leadSourceDistribution(Builder $leadQuery): array
    {
        $rows = (clone $leadQuery)
            ->selectRaw('leads.source, COUNT(*) as aggregate')
            ->groupBy('leads.source')
            ->get();

        $counts = [];

        foreach ($rows as $row) {
            $key = filled($row->source) ? (string) $row->source : '__other__';
            $counts[$key] = ($counts[$key] ?? 0) + (int) $row->aggregate;
        }

        $labels = [];
        $data = [];

        foreach (Lead::SOURCES as $source) {
            $labels[] = ucfirst(str_replace('_', ' ', $source));
            $data[] = (int) ($counts[$source] ?? 0);
            unset($counts[$source]);
        }

        $other = (int) array_sum($counts);

        if ($other > 0) {
            $labels[] = 'Other / Unspecified';
            $data[] = $other;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    protected function dateExpression(string $column): string
    {
        $driver = Lead::query()->getConnection()->getDriverName();

        return $driver === 'sqlite'
            ? "strftime('%Y-%m-%d', {$column})"
            : "DATE({$column})";
    }

    protected function monthExpression(string $column): string
    {
        $driver = Lead::query()->getConnection()->getDriverName();

        return $driver === 'sqlite'
            ? "strftime('%Y-%m', {$column})"
            : "DATE_FORMAT({$column}, '%Y-%m')";
    }
}
