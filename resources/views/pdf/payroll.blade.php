<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Bảng lương {{ $month }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a1a; background: #fff; }
    .header { background: #1a1a1a; color: #fff; padding: 16px 24px; }
    .header h1 { font-size: 16px; font-weight: bold; letter-spacing: -0.5px; }
    .header .meta { font-size: 9px; color: #9ca3af; margin-top: 4px; display: flex; gap: 24px; }
    .content { padding: 20px 24px; }
    .section-title { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #f9fafb; text-align: left; padding: 7px 8px; font-size: 9px; font-weight: bold; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; }
    td { padding: 7px 8px; font-size: 10px; color: #374151; border-bottom: 1px solid #f3f4f6; }
    tr:hover td { background: #f9fafb; }
    .amount  { font-weight: bold; color: #111827; text-align: right; }
    .status-draft   { background: #f3f4f6; color: #6b7280; padding: 2px 6px; font-size: 8px; }
    .status-confirmed { background: #dbeafe; color: #1e40af; padding: 2px 6px; font-size: 8px; }
    .status-paid    { background: #d1fae5; color: #065f46; padding: 2px 6px; font-size: 8px; }
    .summary-row td { background: #1a1a1a; color: #fff; font-weight: bold; border: none; }
    .footer { padding: 12px 24px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; font-size: 9px; color: #9ca3af; margin-top: 20px; }
    .badge { display: inline-block; }
</style>
</head>
<body>

<div class="header">
    <h1>BẢNG LƯƠNG GIÁO VIÊN</h1>
    <div class="meta">
        <span>Tháng: {{ \Carbon\Carbon::parse($month . '-01')->format('m/Y') }}</span>
        <span>Trung tâm: {{ $tenant?->name ?? '—' }}</span>
        <span>Xuất ngày: {{ now()->format('d/m/Y') }}</span>
        <span>Tổng GV: {{ $payrolls->count() }}</span>
    </div>
</div>

<div class="content">

    <div class="section-title">Chi tiết bảng lương</div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Giáo viên</th>
                <th style="text-align:center">Số buổi dạy</th>
                <th style="text-align:right">Lương/buổi</th>
                <th style="text-align:right">Thưởng</th>
                <th style="text-align:right">Khấu trừ</th>
                <th style="text-align:right">Thực lĩnh</th>
                <th style="text-align:center">Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payrolls as $i => $p)
            @php
                $netPay = $p->base_salary + ($p->bonus ?? 0) - ($p->deductions ?? 0);
                $stMap  = ['draft'=>'Nháp', 'confirmed'=>'Xác nhận', 'paid'=>'Đã TT'];
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $p->teacher?->name ?? '—' }}</strong></td>
                <td style="text-align:center">{{ $p->sessions_count ?? '—' }}</td>
                <td class="amount">{{ number_format($p->base_salary) }}đ</td>
                <td class="amount">{{ number_format($p->bonus ?? 0) }}đ</td>
                <td class="amount" style="color:#dc2626">{{ number_format($p->deductions ?? 0) }}đ</td>
                <td class="amount" style="font-size:11px">{{ number_format($netPay) }}đ</td>
                <td style="text-align:center"><span class="badge status-{{ $p->status }}">{{ $stMap[$p->status] ?? $p->status }}</span></td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center; color:#9ca3af; padding:20px">Không có dữ liệu lương tháng này</td>
            </tr>
            @endforelse
        </tbody>
        @if($payrolls->isNotEmpty())
        <tfoot>
            <tr class="summary-row">
                <td colspan="4" style="text-align:right">TỔNG CỘNG</td>
                <td style="text-align:right">{{ number_format($payrolls->sum('bonus')) }}đ</td>
                <td style="text-align:right">{{ number_format($payrolls->sum('deductions')) }}đ</td>
                <td style="text-align:right; font-size:12px">
                    {{ number_format($payrolls->sum(fn($p) => $p->base_salary + ($p->bonus ?? 0) - ($p->deductions ?? 0))) }}đ
                </td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>

</div>

<div class="footer">
    <span>TutorCenter CRM — Bảng lương tháng {{ \Carbon\Carbon::parse($month . '-01')->format('m/Y') }}</span>
    <span>In ngày {{ now()->format('d/m/Y H:i') }}</span>
</div>

</body>
</html>
