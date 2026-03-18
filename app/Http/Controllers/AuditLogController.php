<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with(['user'])
            ->latest('created_at');

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }
        if ($request->filled('q')) {
            $query->where('description', 'like', "%{$request->q}%");
        }

        $logs = $query->paginate(30)->withQueryString();
        $events = AuditLog::select('event')->distinct()->pluck('event');

        return view('audit.index', compact('logs', 'events'));
    }
}
