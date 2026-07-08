<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('tasks.view');
    }

    public function view(User $user, Task $task): bool
    {
        if (! $user->hasPermission('tasks.view')) {
            return false;
        }

        return $user->canAssignTasks() || $task->assigned_to === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('tasks.create');
    }

    public function update(User $user, Task $task): bool
    {
        if (! $user->hasPermission('tasks.update')) {
            return false;
        }

        return $user->canAssignTasks() || $task->assigned_to === $user->id;
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->hasPermission('tasks.delete');
    }
}
