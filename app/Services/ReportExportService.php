<?php

namespace App\Services;

use App\Models\User;
use App\Services\Csv\CsvStreamer;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportService
{
    public function __construct(
        protected ReportService $reports,
        protected CsvStreamer $csv,
    ) {}

    /**
     * @param  array{
     *     date_from: string,
     *     date_to: string,
     *     employee_id: int|null,
     *     source: string|null,
     *     status: string|null
     * }  $filters
     */
    public function download(User $user, string $type, array $filters): StreamedResponse
    {
        return match ($type) {
            'leads' => $this->exportLeads($user, $filters),
            'customers' => $this->exportCustomers($user, $filters),
            'tasks' => $this->exportTasks($user, $filters),
            'performance' => $this->exportPerformance($user, $filters),
            default => abort(404, 'Unknown report export type.'),
        };
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    protected function exportLeads(User $user, array $filters): StreamedResponse
    {
        abort_unless($user->hasPermission('view.leads'), 403);

        $dateFrom = Carbon::parse($filters['date_from'])->startOfDay();
        $dateTo = Carbon::parse($filters['date_to'])->endOfDay();

        $rows = $this->reports
            ->filteredLeadsQuery($user, $filters, $dateFrom, $dateTo)
            ->with('assignee')
            ->orderBy('created_at')
            ->get();

        return $this->csv->download('leads-report.csv', [
            'Name', 'Email', 'Phone', 'Company', 'Source', 'Status', 'Assigned To', 'Estimated Value', 'Created At',
        ], $rows->map(fn ($lead) => [
            $lead->name,
            $lead->email,
            $lead->phone,
            $lead->company,
            $lead->source,
            $lead->status,
            $lead->assignee?->name,
            $lead->estimated_value,
            optional($lead->created_at)?->toDateTimeString(),
        ]));
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    protected function exportCustomers(User $user, array $filters): StreamedResponse
    {
        abort_unless($user->hasPermission('view.customers'), 403);

        $dateFrom = Carbon::parse($filters['date_from'])->startOfDay();
        $dateTo = Carbon::parse($filters['date_to'])->endOfDay();

        $rows = $this->reports
            ->filteredCustomersQuery($dateFrom, $dateTo)
            ->orderBy('created_at')
            ->get();

        return $this->csv->download('customers-report.csv', [
            'Name', 'Email', 'Phone', 'Company', 'Status', 'Created At',
        ], $rows->map(fn ($customer) => [
            $customer->name,
            $customer->email,
            $customer->phone,
            $customer->company_name,
            $customer->status,
            optional($customer->created_at)?->toDateTimeString(),
        ]));
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    protected function exportTasks(User $user, array $filters): StreamedResponse
    {
        abort_unless($user->hasPermission('view.tasks'), 403);

        $dateFrom = Carbon::parse($filters['date_from'])->startOfDay();
        $dateTo = Carbon::parse($filters['date_to'])->endOfDay();

        $rows = $this->reports
            ->filteredTasksQuery($user, $filters, $dateFrom, $dateTo)
            ->with('assignee')
            ->orderBy('created_at')
            ->get();

        return $this->csv->download('tasks-report.csv', [
            'Title', 'Status', 'Priority', 'Assigned To', 'Due Date', 'Created At',
        ], $rows->map(fn ($task) => [
            $task->title,
            $task->status,
            $task->priority,
            $task->assignee?->name,
            $task->due_date,
            optional($task->created_at)?->toDateTimeString(),
        ]));
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    protected function exportPerformance(User $user, array $filters): StreamedResponse
    {
        abort_unless($user->hasPermission('view.leads'), 403);

        $payload = $this->reports->forUser($user, $filters);
        $rows = collect($payload['performance']['by_employee'] ?? []);

        return $this->csv->download('sales-performance-report.csv', [
            'Employee', 'Leads Assigned', 'Leads Converted', 'Conversion Rate %',
        ], $rows->map(fn (array $row) => [
            $row['employee'],
            $row['assigned'],
            $row['converted'],
            $row['conversion_rate'],
        ]));
    }
}
