<?php

namespace App\Http\Requests;

use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReportFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAccessReports() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'employee_id' => ['nullable', 'integer', 'exists:users,id'],
            'source' => ['nullable', Rule::in(Lead::SOURCES)],
            'status' => ['nullable', Rule::in(array_keys(Lead::STATUSES))],
        ];
    }

    /**
     * Normalized filters with defaults (last 90 days → today).
     *
     * @return array{
     *     date_from: string,
     *     date_to: string,
     *     employee_id: int|null,
     *     source: string|null,
     *     status: string|null
     * }
     */
    public function filters(): array
    {
        $validated = $this->validated();

        return [
            'date_from' => $validated['date_from'] ?? now()->subDays(89)->toDateString(),
            'date_to' => $validated['date_to'] ?? now()->toDateString(),
            'employee_id' => isset($validated['employee_id']) ? (int) $validated['employee_id'] : null,
            'source' => $validated['source'] ?? null,
            'status' => $validated['status'] ?? null,
        ];
    }
}
