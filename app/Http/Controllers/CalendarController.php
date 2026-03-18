<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\ClassSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    // Deterministic color palette per class ID (cycles through 8 colors)
    private const COLORS = [
        '#ef4444', '#f97316', '#eab308', '#22c55e',
        '#06b6d4', '#6366f1', '#ec4899', '#8b5cf6',
    ];

    public function index()
    {
        $classes = ClassRoom::orderBy('name')->get(['id', 'name']);
        return view('calendar.index', compact('classes'));
    }

    /**
     * JSON events API for FullCalendar.
     * Supports date-range fetch, teacher role isolation, and optional class_id filter.
     */
    public function events(Request $request)
    {
        $user  = Auth::user();
        $start = $request->get('start');
        $end   = $request->get('end');

        $query = ClassSession::with(['classroom', 'teacher'])
            ->whereBetween('date', [$start, $end]);

        // Teacher / tutor: only see their own sessions
        if (in_array($user->role, ['teacher', 'tutor'])) {
            $query->where('teacher_id', $user->id);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $events = $query->get()->map(function (ClassSession $session) {
            $color = self::COLORS[$session->class_id % 8];
            $dateStr = $session->date->toDateString();

            return [
                'id'    => $session->id,
                'title' => ($session->classroom?->name ?? '?')
                           . ($session->teacher ? ' · ' . $session->teacher->name : ''),
                'start' => "{$dateStr}T" . ($session->start_time ?? '08:00:00'),
                'end'   => "{$dateStr}T" . ($session->end_time   ?? '09:30:00'),
                'color' => $color,
                'extendedProps' => [
                    'class_id'   => $session->class_id,
                    'class_name' => $session->classroom?->name ?? '—',
                    'teacher'    => $session->teacher?->name ?? '—',
                    'status'     => $session->status,
                    'notes'      => $session->lesson_notes,
                    'url'        => route('attendance.show', $session->id),
                ],
            ];
        });

        return response()->json($events);
    }
}
