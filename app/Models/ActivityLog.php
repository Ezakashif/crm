<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'properties',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    public const ACTION_LABELS = [
        'user.created' => 'User created',
        'user.updated' => 'User updated',
        'user.status_changed' => 'User status changed',
        'user.deleted' => 'User deleted',
        'user.login' => 'User logged in',
        'user.logout' => 'User logged out',
        'profile.updated' => 'Profile updated',
        'profile.photo_updated' => 'Profile photo updated',
        'profile.photo_removed' => 'Profile photo removed',
        'customer.created' => 'Customer created',
        'customer.updated' => 'Customer updated',
        'customer.deleted' => 'Customer deleted',
        'lead.created' => 'Lead created',
        'lead.updated' => 'Lead updated',
        'lead.deleted' => 'Lead deleted',
        'lead.converted' => 'Lead converted',
        'lead.status_changed' => 'Lead status changed',
        'lead.created_via_website' => 'Lead created via website',
        'lead.activity_logged' => 'Lead activity logged',
        'task.created' => 'Task created',
        'task.updated' => 'Task updated',
        'task.deleted' => 'Task deleted',
        'task.status_changed' => 'Task status changed',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function actionLabel(): string
    {
        return self::ACTION_LABELS[$this->action] ?? ucfirst(str_replace('.', ' ', $this->action));
    }

    public function description(): string
    {
        $properties = $this->properties ?? [];

        return match ($this->action) {
            'user.status_changed' => sprintf(
                'Status changed from %s to %s',
                ucfirst($properties['from'] ?? 'unknown'),
                ucfirst($properties['to'] ?? 'unknown')
            ),
            'user.created' => sprintf(
                'Created user %s (%s)',
                $properties['name'] ?? 'unknown',
                $properties['email'] ?? ''
            ),
            'user.updated' => sprintf(
                'Updated user %s',
                $properties['name'] ?? ($this->subject?->name ?? 'unknown')
            ),
            'user.deleted' => sprintf(
                'Deleted user %s (%s)',
                $properties['name'] ?? 'unknown',
                $properties['email'] ?? ''
            ),
            'profile.updated' => 'Updated profile information',
            'profile.photo_updated' => 'Uploaded a new profile photo',
            'profile.photo_removed' => 'Removed profile photo',
            'customer.created' => sprintf('Created customer %s', $properties['name'] ?? 'unknown'),
            'customer.updated' => sprintf('Updated customer %s', $properties['name'] ?? ($this->subject?->name ?? 'unknown')),
            'customer.deleted' => sprintf('Deleted customer %s', $properties['name'] ?? 'unknown'),
            'lead.created' => sprintf('Created lead %s', $properties['name'] ?? 'unknown'),
            'lead.updated' => sprintf('Updated lead %s', $properties['name'] ?? ($this->subject?->name ?? 'unknown')),
            'lead.deleted' => sprintf('Deleted lead %s', $properties['name'] ?? 'unknown'),
            'lead.converted' => sprintf('Converted lead %s to customer', $properties['name'] ?? 'unknown'),
            'lead.status_changed' => sprintf(
                'Lead status changed from %s to %s',
                $properties['from'] ?? 'unknown',
                $properties['to'] ?? 'unknown'
            ),
            'lead.created_via_website' => sprintf('Website lead %s created', $properties['name'] ?? ($this->subject?->name ?? 'unknown')),
            'lead.activity_logged' => sprintf(
                'Logged %s activity on lead %s',
                $properties['type'] ?? 'an',
                $properties['lead_name'] ?? 'unknown'
            ),
            'task.created' => sprintf('Created task %s', $properties['title'] ?? 'unknown'),
            'task.updated' => sprintf('Updated task %s', $properties['title'] ?? ($this->subject?->title ?? 'unknown')),
            'task.deleted' => sprintf('Deleted task %s', $properties['title'] ?? 'unknown'),
            'task.status_changed' => sprintf(
                'Task status changed from %s to %s',
                $properties['from'] ?? 'unknown',
                $properties['to'] ?? 'unknown'
            ),
            default => $this->actionLabel(),
        };
    }
}
