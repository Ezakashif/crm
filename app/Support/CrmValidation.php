<?php

namespace App\Support;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CrmValidation
{
    /**
     * Validation rules for creating a lead (manual create + CSV import).
     *
     * @return array<string, mixed>
     */
    public static function leadStoreRules(User $user, bool $forImport = false): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'source' => 'nullable|in:'.implode(',', Lead::SOURCES),
            'estimated_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
        ];

        if ($user->canAssignLeads()) {
            $rules['assigned_to'] = $forImport
                ? ['nullable', 'email', 'max:255', 'exists:users,email']
                : ['nullable', 'exists:users,id'];
        }

        return $rules;
    }

    /**
     * Validation rules for creating a customer (manual create + CSV import).
     *
     * @return array<string, mixed>
     */
    public static function customerStoreRules(bool $forImport = false): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * Validation rules for updating a customer.
     *
     * @return array<string, mixed>
     */
    public static function customerUpdateRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * Validation rules for creating a user (manual create + CSV import).
     *
     * @return array<string, mixed>
     */
    public static function userStoreRules(bool $forImport = false): array
    {
        if ($forImport) {
            return [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'password' => ['required', 'string', Password::defaults()],
                'roles' => ['required', 'string', 'max:255'],
                'status' => ['required', Rule::in(array_keys(User::STATUSES))],
            ];
        }

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['integer', 'exists:roles,id'],
            'status' => ['required', Rule::in(array_keys(User::STATUSES))],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ];
    }
}
