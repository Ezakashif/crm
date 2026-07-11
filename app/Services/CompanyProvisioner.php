<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompanyProvisioner
{
    public function __construct(
        private RbacRoleSynchronizer $roleSynchronizer,
    ) {}

    /**
     * Create a company, sync default roles, and optionally provision the first admin.
     *
     * @param  array{name: string, slug?: string|null, status?: string, admin_name?: string|null, admin_email?: string|null, admin_password?: string|null}  $data
     * @return array{company: Company, admin: User|null}
     */
    public function provision(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $slug = $data['slug'] ?? null;
            $slug = filled($slug) ? Str::slug((string) $slug) : Str::slug($data['name']);

            $company = Company::query()->create([
                'name' => $data['name'],
                'slug' => $this->uniqueSlug($slug),
                'status' => $data['status'] ?? Company::STATUS_ACTIVE,
            ]);

            $this->roleSynchronizer->syncDefaultRolesForCompany($company);

            $admin = null;

            if (filled($data['admin_email'] ?? null)) {
                $admin = new User([
                    'name' => $data['admin_name'] ?: 'Administrator',
                    'email' => $data['admin_email'],
                    'password' => $data['admin_password'] ?: Str::password(12),
                    'role' => 'admin',
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'is_super_admin' => false,
                ]);
                $admin->company_id = $company->id;
                $admin->save();
                $admin->syncRolesFromLegacyColumn();
            }

            return compact('company', 'admin');
        });
    }

    private function uniqueSlug(string $base): string
    {
        $slug = $base !== '' ? $base : 'company';
        $candidate = $slug;
        $i = 1;

        while (Company::query()->where('slug', $candidate)->exists()) {
            $candidate = $slug.'-'.$i;
            $i++;
        }

        return $candidate;
    }
}
