<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user->hasPermission('view.activity_logs') && ! $user->hasPermission('view_own.activity_logs')) {
            abort(403, 'Unauthorized action.');
        }

        $canViewAll = $user->hasPermission('view.activity_logs');

        $query = ActivityLog::with(['actor', 'subject'])->latest();

        if ($canViewAll) {
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
        } else {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('activity-logs.index', [
            'logs' => $logs,
            'users' => $canViewAll ? User::orderBy('name')->get() : collect(),
            'actions' => ActivityLog::ACTION_LABELS,
            'canViewAll' => $canViewAll,
        ]);
    }
}
