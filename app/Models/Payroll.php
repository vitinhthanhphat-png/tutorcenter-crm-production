<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    use BelongsToTenant, HasAuditLog;


    protected $fillable = [
        'tenant_id', 'branch_id', 'user_id', 'month',
        'total_sessions', 'total_hours', 'rate_per_session', 'rate_per_hour',
        'base_salary', 'session_pay', 'bonus', 'deduction', 'total',
        'status', 'note', 'created_by',
    ];

    protected $casts = [
        'total_sessions'  => 'integer',
        'total_hours'     => 'integer',
        'rate_per_session'=> 'integer',
        'rate_per_hour'   => 'integer',
        'base_salary'     => 'integer',
        'session_pay'     => 'integer',
        'bonus'           => 'integer',
        'deduction'       => 'integer',
        'total'           => 'integer',
    ];

    public function calculateTotal(): int
    {
        $sessionPay  = $this->total_sessions * $this->rate_per_session;
        $hourPay     = $this->total_hours * $this->rate_per_hour;
        return (int)($this->base_salary + $sessionPay + $hourPay + $this->bonus - $this->deduction);
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'draft'     => '📝 Nháp',
            'confirmed' => '✅ Đã xác nhận',
            'paid'      => '💰 Đã thanh toán',
            default     => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'draft'     => 'bg-gray-100 text-gray-500',
            'confirmed' => 'bg-blue-50 text-blue-700',
            'paid'      => 'bg-green-50 text-green-700',
            default     => 'bg-gray-100 text-gray-500',
        };
    }

    public function teacher(): BelongsTo  { return $this->belongsTo(User::class, 'user_id'); }
    public function tenant(): BelongsTo   { return $this->belongsTo(Tenant::class); }
    public function branch(): BelongsTo   { return $this->belongsTo(Branch::class); }
    public function creator(): BelongsTo  { return $this->belongsTo(User::class, 'created_by'); }
}
