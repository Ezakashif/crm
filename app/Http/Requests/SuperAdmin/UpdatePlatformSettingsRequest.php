<?php

namespace App\Http\Requests\SuperAdmin;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlatformSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'platform_name' => ['required', 'string', 'max:255'],
            'platform_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048', 'dimensions:max_width=2000,max_height=2000'],
            'remove_logo' => ['nullable', 'boolean'],
            'platform_favicon' => ['nullable', 'file', 'mimes:ico,png,svg', 'max:512'],
            'remove_favicon' => ['nullable', 'boolean'],
            'default_timezone' => ['required', 'timezone'],
            'default_currency' => ['required', 'string', 'size:3'],
            'default_plan_id' => ['nullable', 'integer', 'exists:plans,id'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],
            'mail_from_address' => ['nullable', 'email', 'max:255'],
            'registration_enabled' => ['nullable', 'boolean'],
            'email_verification_required' => ['nullable', 'boolean'],
            'maintenance_mode' => ['nullable', 'boolean'],
            'trial_duration_days' => ['required', 'integer', 'min:1', 'max:365'],
            'default_company_status' => ['required', Rule::in(array_keys(Company::STATUSES))],
        ];
    }
}
