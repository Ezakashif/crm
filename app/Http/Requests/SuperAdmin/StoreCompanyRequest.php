<?php

namespace App\Http\Requests\SuperAdmin;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() === true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'subscription_status' => $this->input('subscription_status', Company::SUBSCRIPTION_TRIAL),
            'status' => $this->input('status', Company::STATUS_ACTIVE),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:100',
                'alpha_dash',
                Rule::unique('companies', 'slug')->whereNull('deleted_at'),
            ],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048', 'dimensions:max_width=2000,max_height=2000'],
            'status' => ['required', Rule::in(array_keys(Company::STATUSES))],
            'subscription_status' => ['required', Rule::in(array_keys(Company::SUBSCRIPTION_STATUSES))],
            'plan_id' => ['nullable', 'integer', 'exists:plans,id'],
            'trial_ends_at' => ['nullable', 'date'],
            'admin_name' => ['nullable', 'string', 'max:255'],
            'admin_email' => [
                'nullable',
                'required_with:admin_password',
                'email',
                'max:255',
                Rule::unique('users', 'email')->where(function ($query) {
                    // Ignore users still attached to soft-deleted companies
                    // (those emails are also archived on delete, but keep this
                    // defensive for older soft-deletes before the fix).
                    $query->where(function ($inner) {
                        $inner->whereNull('company_id')
                            ->orWhereIn('company_id', function ($companies) {
                                $companies->select('id')
                                    ->from('companies')
                                    ->whereNull('deleted_at');
                            });
                    });
                }),
            ],
            'admin_password' => ['nullable', 'required_with:admin_email', Password::defaults()],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'admin_password.required_with' => 'An admin password is required when creating a company admin account.',
        ];
    }
}
