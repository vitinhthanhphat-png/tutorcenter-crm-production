<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'enrollment_id', 'cashier_id',
        'invoice_code', 'amount', 'payment_method',
        'transaction_date', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:0',
        'transaction_date' => 'date',
    ];

    protected static function booted(): void
    {
        // After creating an invoice, update the enrollment's paid_amount
        static::created(function (Invoice $invoice) {
            $enrollment = $invoice->enrollment;
            $totalPaid = $enrollment->invoices()->sum('amount');
            $enrollment->update(['paid_amount' => $totalPaid]);
        });
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
}
