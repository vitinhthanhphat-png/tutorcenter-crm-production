<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    use HasFactory, BelongsToTenant, HasAuditLog;


    protected $fillable = [
        'tenant_id', 'student_id', 'class_id',
        'final_price', 'paid_amount',
        'discount_note', 'status', 'enrolled_by', 'notes',
        'reserved_at', 'reservation_ends_at', 'reservation_note',
    ];

    protected $casts = [
        'final_price'         => 'decimal:0',
        'paid_amount'         => 'decimal:0',
        'reserved_at'         => 'date',
        'reservation_ends_at' => 'date',
    ];

    // Outstanding debt
    public function getDebtAttribute(): float
    {
        return max(0, $this->final_price - $this->paid_amount);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function enrolledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enrolled_by');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
