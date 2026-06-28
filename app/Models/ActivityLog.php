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
            default => $this->actionLabel(),
        };
    }
}
