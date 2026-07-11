<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Support\CurrentCompany;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    public static function log(
        string $action,
        ?Model $subject = null,
        array $properties = [],
        ?int $userId = null,
    ): ActivityLog {
        $companyId = $subject?->getAttribute('company_id')
            ?? app(CurrentCompany::class)->id();

        $log = new ActivityLog([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'properties' => $properties ?: null,
            'ip_address' => request()->ip(),
        ]);

        if ($companyId !== null) {
            $log->company_id = $companyId;
        }

        $log->save();

        return $log;
    }
}
