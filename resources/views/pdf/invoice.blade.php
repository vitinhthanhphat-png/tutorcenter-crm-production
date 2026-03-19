<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Phiếu thu {{ $invoice->invoice_code ?? $invoice->id }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; background: #fff; }
    .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #dc2626; padding-bottom: 16px; margin-bottom: 20px; padding: 20px 24px 16px; }
    .brand h2 { font-size: 16px; font-weight: bold; color: #dc2626; }
    .brand p  { font-size: 9px; color: #6b7280; margin-top: 2px; }
    .doc-info { text-align: right; }
    .doc-info h1 { font-size: 14px; font-weight: bold; text-transform: uppercase; color: #111827; }
    .doc-info .code { font-family: monospace; font-size: 12px; color: #dc2626; margin-top: 4px; }
    .doc-info .date { font-size: 9px; color: #9ca3af; margin-top: 2px; }
    .content { padding: 0 24px 24px; }
    .parties { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
    .party { padding: 12px; background: #f9fafb; border: 1px solid #e5e7eb; }
    .party-title { font-size: 9px; font-weight: bold; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
    .party p { font-size: 10px; color: #374151; margin-bottom: 2px; }
    .party strong { color: #111827; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    th { background: #1a1a1a; color: #fff; text-align: left; padding: 7px 10px; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; }
    td { padding: 8px 10px; font-size: 10px; color: #374151; border-bottom: 1px solid #f3f4f6; }
    .total-row td { background: #dc2626; color: #fff; font-weight: bold; font-size: 12px; border: none; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 2px; font-size: 9px; font-weight: bold; }
    .badge-paid   { background: #d1fae5; color: #065f46; }
    .badge-pending{ background: #fef3c7; color: #92400e; }
    .badge-overdue{ background: #fee2e2; color: #991b1b; }
    .note-box { background: #f9fafb; border: 1px solid #e5e7eb; padding: 10px; font-size: 10px; color: #374151; margin-bottom: 16px; }
    .signature-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 24px; }
    .signature { text-align: center; }
    .signature .label { font-size: 9px; color: #6b7280; text-transform: uppercase; font-weight: bold; margin-bottom: 4px; }
    .signature .name  { font-size: 10px; color: #374151; font-style: italic; }
    .sig-line { border-bottom: 1px solid #d1d5db; height: 36px; margin: 8px 0; }
    .footer { padding: 12px 24px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; font-size: 9px; color: #9ca3af; }
    .pay-map span { padding: 3px 8px; border: 1px solid #d1d5db; font-size: 10px; display: inline-block; }
</style>
</head>
<body>

<div class="header">
    <div class="brand">
        <h2>{{ $tenant?->name ?? 'TutorCenter CRM' }}</h2>
        <p>Hệ thống quản lý trung tâm giảng dạy</p>
    </div>
    <div class="doc-info">
        <h1>Phiếu Thu Học Phí</h1>
        <div class="code">{{ $invoice->invoice_code ?? ('#' . $invoice->id) }}</div>
        <div class="date">Ngày: {{ $invoice->transaction_date?->format('d/m/Y') }}</div>
    </div>
</div>

<div class="content">

    <div class="parties">
        <div class="party">
            <div class="party-title">Đơn vị thu</div>
            <p><strong>{{ $tenant?->name ?? 'Trung tâm' }}</strong></p>
            <p>Thu ngân: {{ $invoice->cashier?->name ?? '—' }}</p>
        </div>
        <div class="party">
            <div class="party-title">Người nộp</div>
            <p><strong>{{ $invoice->enrollment?->student?->name ?? '—' }}</strong></p>
            <p>SĐT: {{ $invoice->enrollment?->student?->phone ?? '—' }}</p>
            <p>Lớp: {{ $invoice->enrollment?->classroom?->name ?? '—' }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:50%">Nội dung</th>
                <th>Hình thức TT</th>
                <th>Trạng thái</th>
                <th style="text-align:right">Số tiền</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    Học phí lớp: <strong>{{ $invoice->enrollment?->classroom?->name ?? '—' }}</strong>
                    @if($invoice->notes)<br><span style="color:#6b7280;font-size:9px">{{ $invoice->notes }}</span>@endif
                </td>
                <td>
                    @php $payMap = ['cash'=>'Tiền mặt','transfer'=>'CK ngân hàng','card'=>'Thẻ','other'=>'Khác']; @endphp
                    {{ $payMap[$invoice->payment_method] ?? $invoice->payment_method }}
                </td>
                <td>
                    @php $statusMap = ['paid'=>'Đã TT','pending'=>'Chưa TT','overdue'=>'Quá hạn']; @endphp
                    <span class="badge badge-{{ $invoice->status }}">{{ $statusMap[$invoice->status] ?? $invoice->status }}</span>
                </td>
                <td style="text-align:right; font-weight:bold">{{ number_format($invoice->amount) }}đ</td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" style="text-align:right">TỔNG CỘNG</td>
                <td style="text-align:right">{{ number_format($invoice->amount) }}đ</td>
            </tr>
        </tfoot>
    </table>

    @if($invoice->notes)
    <div class="note-box">
        <strong>Ghi chú:</strong> {{ $invoice->notes }}
    </div>
    @endif

    <div class="signature-row">
        <div class="signature">
            <div class="label">Người nộp tiền</div>
            <div class="sig-line"></div>
            <div class="name">{{ $invoice->enrollment?->student?->name ?? '—' }}</div>
        </div>
        <div class="signature">
            <div class="label">Thu ngân</div>
            <div class="sig-line"></div>
            <div class="name">{{ $invoice->cashier?->name ?? '—' }}</div>
        </div>
    </div>

</div>

<div class="footer">
    <span>TutorCenter CRM — Phiếu thu học phí</span>
    <span>In ngày {{ now()->format('d/m/Y H:i') }}</span>
</div>

</body>
</html>
