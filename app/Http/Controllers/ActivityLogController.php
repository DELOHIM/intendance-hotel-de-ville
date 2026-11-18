<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::with('user')
            ->when($request->user_id, fn ($q, $v) => $q->where('user_id', $v))
            ->when($request->action, fn ($q, $v) => $q->where('action', 'like', "%$v%"))
            ->when($request->du, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->au, fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->latest()->paginate(30)->withQueryString();

        return view('logs.index', ['logs' => $logs, 'users' => User::orderBy('name')->get(['id', 'name'])]);
    }
}
