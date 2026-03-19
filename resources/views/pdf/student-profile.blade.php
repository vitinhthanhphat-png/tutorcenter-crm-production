<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Hồ sơ học sinh - {{ $student->name }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; background: #fff; }
    .header { background: #dc2626; color: #fff; padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; }
    .header h1 { font-size: 18px; font-weight: bold; letter-spacing: -0.5px; }
    .header .sub { font-size: 10px; opacity: 0.8; margin-top: 4px; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 2px; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
    .badge-lead     { background: #fef3c7; color: #92400e; }
    .badge-studying { background: #d1fae5; color: #065f46; }
    .badge-dropped  { background: #f3f4f6; color: #6b7280; }
    .badge-reserved { background: #dbeafe; color: #1e40af; }
    .content { padding: 24px; }
    .section { margin-bottom: 20px; }
    .section-title { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; border-bottom: 1px solid #e5e7eb; padding-bottom: 6px; margin-bottom: 12px; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 24px; }
    .info-item label { font-size: 9px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 2px; }
    .info-item span { font-size: 11px; color: #111827; font-weight: 500; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th { background: #f9fafb; text-align: left; padding: 6px 8px; font-size: 9px; font-weight: bold; color: #6b7280; text-transform: uppercase; border-bottom: 1px solid #e5e7eb; }
    td { padding: 7px 8px; font-size: 10px; color: #374151; border-bottom: 1px solid #f3f4f6; }
    .footer { margin-top: 32px; padding-top: 12px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; font-size: 9px; color: #9ca3af; }
    .amount { font-weight: bold; color: #111827; }
    .debt    { color: #dc2626; font-weight: bold; }
</style>
</head>
<body>
<div class="header">
    <div>
        <div class="sub">TutorCenter CRM</div>
        <h1>HỒ SƠ HỌC SINH</h1>
    </div>
    <div style="text-align:right;">
        <div style="font-size:10px; opacity:.8;">Xuất ngày: {{ now()->format('d/m/Y') }}</div>
        <div style="font-size:13px; font-weight:bold; margin-top:4px;">{{ $student->name }}</div>
    </div>
</div>

<div class="content">

    <div class="section">
        <div class="section-title">Thông tin cá nhân</div>
        <div class="info-grid">
            <div class="info-item">
                <label>Họ tên</label>
                <span>{{ $student->name }}</span>
            </div>
            <div class="info-item">
                <label>Trạng thái</label>
                <span>
                    @php $sMap = ['lead'=>'Lead','studying'=>'Đang học','dropped'=>'Ngừng học','reserved'=>'Bảo lưu','graduated'=>'Tốt nghiệp']; @endphp
                    <span class="badge badge-{{ $student->status }}">{{ $sMap[$student->status] ?? $student->status }}</span>
                </span>
            </div>
            <div class="info-item">
                <label>Số điện thoại</label>
                <span>{{ $student->phone ?? '—' }}</span>
            </div>
            <div class="info-item">
                <label>Email</label>
                <span>{{ $student->email ?? '—' }}</span>
            </div>
            <div class="info-item">
                <label>Ngày sinh</label>
                <span>{{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d/m/Y') : '—' }}</span>
            </div>
            <div class="info-item">
                <label>Trường học</label>
                <span>{{ $student->school ?? '—' }}</span>
            </div>
            <div class="info-item">
                <label>Chi nhánh</label>
                <span>{{ $student->branch?->name ?? '—' }}</span>
            </div>
            <div class="info-item">
                <label>Nguồn</label>
                <span>{{ ucfirst($student->lead_source ?? '—') }}</span>
            </div>
        </div>
        @if($student->notes)
        <div style="margin-top:10px; padding:8px; background:#f9fafb; border-left:3px solid #dc2626; font-size:10px; color:#374151;">
            <strong>Ghi chú:</strong> {{ $student->notes }}
        </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Lịch sử ghi danh</div>
        @if($student->enrollments->isEmpty())
        <p style="color:#9ca3af; font-size:10px; padding:8px 0;">Chưa có lịch sử ghi danh.</p>
        @else
        <table>
            <thead>
                <tr>
                    <th>Lớp học</th>
                    <th>Học phí</th>
                    <th>Đã trả</th>
                    <th>Còn lại</th>
                    <th>Trạng thái</th>
                    <th>Ngày ghi danh</th>
                </tr>
            </thead>
            <tbody>
                @foreach($student->enrollments as $en)
                @php
                    $debt = max(0, $en->final_price - $en->paid_amount);
                    $enMap = ['active'=>'Đang học','completed'=>'Hoàn thành','cancelled'=>'Hủy','reserved'=>'Bảo lưu'];
                @endphp
                <tr>
                    <td><strong>{{ $en->classroom?->name ?? '—' }}</strong></td>
                    <td class="amount">{{ number_format($en->final_price) }}đ</td>
                    <td>{{ number_format($en->paid_amount) }}đ</td>
                    <td class="{{ $debt > 0 ? 'debt' : '' }}">{{ number_format($debt) }}đ</td>
                    <td>{{ $enMap[$en->status] ?? $en->status }}</td>
                    <td>{{ $en->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

</div>

<div style="padding: 0 24px;">
    <div class="footer">
        <span>TutorCenter CRM — Hồ sơ học sinh</span>
        <span>{{ now()->format('d/m/Y H:i') }}</span>
    </div>
</div>
</body>
</html>
