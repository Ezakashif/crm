<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view.leads');
    }

    public function view(User $user, Lead $lead): bool
    {
        return $user->hasPermission('view.leads');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create.leads');
    }

    public function update(User $user, Lead $lead): bool
    {
        return $user->hasPermission('update.leads');
    }

    public function delete(User $user, Lead $lead): bool
    {
        return $user->hasPermission('delete.leads');
    }

    public function convert(User $user, Lead $lead): bool
    {
        return $user->hasPermission('convert.leads');
    }

    public function createActivity(User $user, Lead $lead): bool
    {
        return $user->hasPermission('log.leads');
    }
}
