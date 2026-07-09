<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    public const SOURCES = [
        'website',
        'facebook',
        'referral',
        'whatsapp',
        'linkedin',
        'cold_call',
    ];

    public const STATUSES = [
        'new' => 'New',
        'contacted' => 'Contacted',
        'qualified' => 'Qualified',
        'proposal_sent' => 'Proposal Sent',
        'won' => 'Won',
        'lost' => 'Lost',
    ];

    protected $fillable = [
        'created_by',
        'assigned_to',
        'name',
        'email',
        'phone',
        'company',
        'source',
        'status',
        'sort_order',
        'estimated_value',
        'notes',
        'follow_up_date',
        'follow_up_reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'follow_up_date' => 'date',
            'follow_up_reminder_sent_at' => 'datetime',
            'estimated_value' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (Lead $lead) {
            if ($lead->isDirty('follow_up_date')) {
                $lead->follow_up_reminder_sent_at = null;
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)->orderByDesc('occurred_at');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function whatsAppUrl(): ?string
    {
        if (! filled($this->phone)) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $this->phone);

        return filled($digits) ? 'https://wa.me/'.$digits : null;
    }

    public function callUrl(): ?string
    {
        if (! filled($this->phone)) {
            return null;
        }

        $digits = preg_replace('/[^\d+]/', '', $this->phone);

        return filled($digits) ? 'tel:'.$digits : null;
    }

    public function emailUrl(): ?string
    {
        return filled($this->email) ? 'mailto:'.$this->email : null;
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! filled($term)) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($term) {
            $builder->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
                ->orWhere('company', 'like', "%{$term}%");
        });
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if (! filled($status)) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->canViewAllLeads()) {
            return $query;
        }

        return $query->where('assigned_to', $user->id);
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

    public function scopeSource(Builder $query, ?string $source): Builder
    {
        if (! filled($source)) {
            return $query;
        }

        return $query->where('source', $source);
    }

    public function scopeDueForFollowUpReminder(Builder $query): Builder
    {
        return $query
            ->whereNotNull('assigned_to')
            ->whereNotNull('follow_up_date')
            ->whereDate('follow_up_date', '<=', today())
            ->whereNull('follow_up_reminder_sent_at')
            ->whereNotIn('status', ['won', 'lost'])
            ->whereHas('assignee', fn (Builder $assignee) => $assignee->where('status', 'active'));
    }
}
