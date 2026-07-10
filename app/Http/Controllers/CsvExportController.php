<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Task;
use App\Services\Csv\ListExportService;
use App\Services\CustomerListQueryService;
use App\Services\LeadListQueryService;
use App\Services\TaskListQueryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExportController extends Controller
{
    public function __construct(
        protected ListExportService $exports,
        protected LeadListQueryService $leads,
        protected CustomerListQueryService $customers,
        protected TaskListQueryService $tasks,
    ) {}

    public function leads(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Lead::class);

        $filters = $request->validate($this->leads->filterRules());

        return $this->exports->leads($request->user(), $filters);
    }

    public function customers(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Customer::class);

        $filters = $request->validate($this->customers->filterRules());

        return $this->exports->customers($filters);
    }

    public function tasks(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Task::class);

        $filters = $request->validate($this->tasks->filterRules());

        return $this->exports->tasks($request->user(), $filters);
    }
}
