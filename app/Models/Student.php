<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    use HasFactory, BelongsToTenant, HasAuditLog;


    protected $fillable = [
        'tenant_id', 'branch_id', 'user_id', 'parent_id',
        'name', 'phone', 'dob', 'school',
        'status', 'lead_source', 'lead_status', 'notes',
    ];

    protected $casts = ['dob' => 'date'];

    // --- Status Helpers ---
    public function isLead(): bool
    {
        return $this->status === 'lead';
    }

    public function convert(): void
    {
        $this->update(['status' => 'studying']);
    }

    // --- Scopes ---
    public function scopeLeads($query)
    {
        return $query->where('status', 'lead');
    }

    public function scopeStudying($query)
    {
        return $query->where('status', 'studying');
    }

    // --- Relationships ---
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentProfile::class, 'parent_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
