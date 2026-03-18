<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'branch_id', 'name', 'phone', 'email', 'parent_name',
        'status', 'source', 'interested_course', 'note',
        'assigned_to', 'follow_up_at', 'converted_to_student_id', 'converted_at',
    ];

    protected $casts = [
        'follow_up_at' => 'date',
        'converted_at' => 'datetime',
    ];

    // --- Status helpers ---
    public static function statuses(): array
    {
        return [
            'new'        => ['label' => 'Mới vào',       'color' => 'bg-gray-100 text-gray-600'],
            'contacted'  => ['label' => 'Đã liên hệ',   'color' => 'bg-blue-50 text-blue-700'],
            'consulting' => ['label' => 'Đang tư vấn',  'color' => 'bg-yellow-50 text-yellow-700'],
            'test_booked'=> ['label' => 'Hẹn test',     'color' => 'bg-purple-50 text-purple-700'],
            'registered' => ['label' => 'Đã đăng ký',   'color' => 'bg-green-50 text-green-700'],
            'lost'       => ['label' => 'Mất lead',     'color' => 'bg-red-50 text-red-600'],
        ];
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status]['label'] ?? $this->status;
    }

    public function statusColor(): string
    {
        return self::statuses()[$this->status]['color'] ?? 'bg-gray-100 text-gray-500';
    }

    // --- Relationships ---
    public function tenant(): BelongsTo   { return $this->belongsTo(Tenant::class); }
    public function branch(): BelongsTo   { return $this->belongsTo(Branch::class); }
    public function assignedTo(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
    public function student(): BelongsTo  { return $this->belongsTo(Student::class, 'converted_to_student_id'); }

    // --- Scopes ---
    public function scopePending($q) { return $q->whereNotIn('status', ['registered', 'lost']); }
}
