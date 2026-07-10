<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'assigned_to',
        'customer_id',
        'lead_id',
        'title',
        'description',
        'priority',
        'status',
        'sort_order',
        'due_date',
        'completed_at',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->canViewAllTasks()) {
            return $query;
        }

        return $query->where($query->getModel()->getTable().'.assigned_to', $user->id);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! filled($term)) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($term) {
            $builder->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if (! filled($status)) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function scopePriority(Builder $query, ?string $priority): Builder
    {
        if (! filled($priority)) {
            return $query;
        }

        return $query->where('priority', $priority);
    }

    public function scopeAssignedTo(Builder $query, ?string $userId): Builder
    {
        if (! filled($userId)) {
            return $query;
        }

        if ($userId === 'unassigned') {
            return $query->whereNull('assigned_to');
        }

        return $query->where('assigned_to', $userId);
    }
}
