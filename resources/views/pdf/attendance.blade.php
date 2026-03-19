<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Báo cáo điểm danh - {{ $classroom->name }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1a1a1a; background: #fff; }
    .header { background: #dc2626; color: #fff; padding: 14px 20px; display: flex; justify-content: space-between; align-items: center; }
    .header h1 { font-size: 14px; font-weight: bold; }
    .header .meta { font-size: 9px; opacity: .8; margin-top: 3px; }
    .meta-right { text-align: right; font-size: 9px; }
    .content { padding: 16px 20px; }
    table { width: 100%; border-collapse: collapse; font-size: 8.5px; }
    th { background: #1a1a1a; color: #fff; text-align: center; padding: 5px 4px; font-size: 8px; font-weight: bold; white-space: nowrap; }
    th.name-col { text-align: left; min-width: 120px; }
    td { padding: 5px 4px; border-bottom: 1px solid #f3f4f6; text-align: center; }
    td.name-col { text-align: left; font-weight: 500; color: #111827; }
    tr:nth-child(even) td { background: #fafafa; }
    .p { color: #16a34a; font-weight: bold; }   /* Present */
    .a { color: #dc2626; font-weight: bold; }   /* Absent */
    .l { color: #d97706; }                       /* Late */
    .e { color: #9ca3af; }                       /* Excused */
    .summary { display: flex; gap: 20px; margin-bottom: 14px; }
    .stat { background: #f9fafb; border: 1px solid #e5e7eb; padding: 8px 12px; flex: 1; }
    .stat-label { font-size: 8px; color: #9ca3af; text-transform: uppercase; margin-bottom: 2px; }
    .stat-value { font-size: 14px; font-weight: bold; color: #111827; }
    .footer { padding: 10px 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; font-size: 8px; color: #9ca3af; margin-top: 12px; }
    .legend { display: flex; gap: 12px; font-size: 8px; color: #6b7280; margin-bottom: 10px; }
    .legend span { display: inline-flex; align-items: center; gap: 3px; }
</style>
</head>
<body>

<div class="header">
    <div>
        <div class="meta">{{ $tenant?->name ?? 'TutorCenter CRM' }}</div>
        <h1>BÁO CÁO ĐIỂM DANH — {{ strtoupper($classroom->name) }}</h1>
    </div>
    <div class="meta-right">
        <div>Giáo viên: {{ $classroom->teacher?->name ?? '—' }}</div>
        <div style="margin-top:4px">Xuất ngày: {{ now()->format('d/m/Y') }}</div>
    </div>
</div>

<div class="content">

    @php
        $sessions = $classroom->sessions;
        $enrollments = $classroom->enrollments->where('status', 'active');
        $totalSessions = $sessions->count();
        $totalStudents = $enrollments->count();
    @endphp

    <div class="summary">
        <div class="stat">
            <div class="stat-label">Tổng buổi học</div>
            <div class="stat-value">{{ $totalSessions }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Số học sinh</div>
            <div class="stat-value">{{ $totalStudents }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Buổi đã hoàn thành</div>
            <div class="stat-value">{{ $sessions->where('status', 'completed')->count() }}</div>
        </div>
    </div>

    <div class="legend">
        <span><span class="p">✓</span> Có mặt</span>
        <span><span class="a">✗</span> Vắng KP</span>
        <span><span class="l">L</span> Muộn</span>
        <span><span class="e">P</span> Có phép</span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="name-col">#</th>
                <th class="name-col">Học sinh</th>
                @foreach($sessions->take(20) as $session)
                <th>{{ \Carbon\Carbon::parse($session->date)->format('d/m') }}</th>
                @endforeach
                <th>CM</th>
                <th>Vắng</th>
                <th>%</th>
            </tr>
        </thead>
        <tbody>
            @foreach($enrollments as $i => $enrollment)
            @php
                $student = $enrollment->student;
                $present = 0; $absent = 0;
                $statusMap = [
                    'present' => ['✓', 'p'],
                    'absent_no_leave' => ['✗', 'a'],
                    'absent_with_leave' => ['P', 'e'],
                    'late' => ['L', 'l'],
                ];
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="name-col">{{ $student->name }}</td>
                @foreach($sessions->take(20) as $session)
                @php
                    $att = $session->attendances->firstWhere('student_id', $student->id);
                    if ($att) {
                        [$sym, $cls] = $statusMap[$att->status] ?? ['—', ''];
                        if ($att->status === 'present' || $att->status === 'late') $present++;
                        if ($att->status === 'absent_no_leave') $absent++;
                    } else { $sym = '—'; $cls = ''; }
                @endphp
                <td class="{{ $cls }}">{{ $sym }}</td>
                @endforeach
                <td><strong>{{ $present }}</strong></td>
                <td style="color:#dc2626">{{ $absent }}</td>
                <td>{{ $totalSessions > 0 ? round($present / $totalSessions * 100) : 0 }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>

<div class="footer">
    <span>TutorCenter CRM — Báo cáo điểm danh lớp {{ $classroom->name }}</span>
    <span>In ngày {{ now()->format('d/m/Y H:i') }}</span>
</div>

</body>
</html>
