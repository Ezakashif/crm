<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class WebsiteLeadService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Lead
    {
        $validated = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|required_without:phone',
            'phone' => 'nullable|string|max:50|required_without:email',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:5000',
            'message' => 'nullable|string|max:5000',
        ])->validate();

        $createdById = $this->resolveCreatedByUserId();

        $sortOrder = Lead::where('status', 'new')->max('sort_order') + 1;

        $lead = Lead::create([
            'created_by' => $createdById,
            'assigned_to' => null,
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'company' => $validated['company'] ?? null,
            'source' => 'website',
            'status' => 'new',
            'sort_order' => $sortOrder,
            'notes' => $validated['notes'] ?? $validated['message'] ?? null,
        ]);

        ActivityLogger::log('lead.created_via_website', $lead, [], $createdById);

        $initialMessage = $validated['notes'] ?? $validated['message'] ?? null;

        if (filled($initialMessage)) {
            LeadActivity::log(
                $lead,
                'note',
                $initialMessage,
                userId: $createdById,
            );
        }

        return $lead;
    }

    public function resolveCreatedByUserId(): int
    {
        $email = config('website_leads.created_by_email');

        if (filled($email)) {
            $user = User::query()
                ->where('email', $email)
                ->where('status', 'active')
                ->first();

            if ($user) {
                return $user->id;
            }
        }

        $admin = User::query()
            ->where('role', 'admin')
            ->where('status', 'active')
            ->orderBy('id')
            ->first();

        if ($admin) {
            return $admin->id;
        }

        abort(503, 'Website lead webhook has no active admin user to own new leads.');
    }
}
