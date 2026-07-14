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
            'slug' => ['nullable', 'string', 'max:100', 'alpha_dash', 'unique:companies,slug'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048', 'dimensions:max_width=2000,max_height=2000'],
            'status' => ['required', Rule::in(array_keys(Company::STATUSES))],
            'subscription_status' => ['required', Rule::in(array_keys(Company::SUBSCRIPTION_STATUSES))],
            'plan_id' => ['nullable', 'integer', 'exists:plans,id'],
            'trial_ends_at' => ['nullable', 'date'],
            'admin_name' => ['nullable', 'string', 'max:255'],
            'admin_email' => ['nullable', 'required_with:admin_password', 'email', 'max:255', 'unique:users,email'],
            'admin_password' => ['nullable', 'required_with:admin_email', Password::defaults()],
        ];
    }
}
