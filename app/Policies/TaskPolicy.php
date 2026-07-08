<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view.tasks');
    }

    public function view(User $user, Task $task): bool
    {
        if (! $user->hasPermission('view.tasks')) {
            return false;
        }

        return $user->canAssignTasks() || $task->assigned_to === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create.tasks');
    }

    public function update(User $user, Task $task): bool
    {
        if (! $user->hasPermission('update.tasks')) {
            return false;
        }

        return $user->canAssignTasks() || $task->assigned_to === $user->id;
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->hasPermission('delete.tasks');
    }
}
