<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispatchRequest extends Model
{
    protected $fillable = [
        'requester_id', 'user_id', 'target_tenant_id', 'target_branch_id',
        'role_override', 'note', 'status', 'reviewed_by', 'reviewed_at', 'review_note',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // --- Relationships ---
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function targetTenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'target_tenant_id');
    }

    public function targetBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'target_branch_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // --- Helpers ---
    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isApproved(): bool  { return $this->status === 'approved'; }
    public function isRejected(): bool  { return $this->status === 'rejected'; }
    public function isCancelled(): bool { return $this->status === 'cancelled'; }

    public function statusLabel(): string
    {
        return match($this->status) {
            'pending'   => '⏳ Chờ duyệt',
            'approved'  => '✅ Đã duyệt',
            'rejected'  => '❌ Từ chối',
            'cancelled' => '⊘ Đã hủy',
            default     => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'pending'   => 'text-amber-600 bg-amber-50',
            'approved'  => 'text-green-700 bg-green-50',
            'rejected'  => 'text-red-600 bg-red-50',
            'cancelled' => 'text-gray-400 bg-gray-100',
            default     => 'text-gray-600',
        };
    }

    // --- Scopes ---
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
