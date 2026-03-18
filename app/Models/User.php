<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'tenant_id', 'branch_id', 'role', 'phone', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    // ──────────────────────────────────────────────
    // ROLE HELPERS
    // ──────────────────────────────────────────────
    public function isSuperAdmin(): bool    { return $this->role === 'super_admin'; }
    public function isCenterManager(): bool { return $this->role === 'center_manager'; }
    public function isBranchManager(): bool { return $this->role === 'branch_manager'; }
    public function isOperations(): bool    { return $this->role === 'operations'; }
    public function isAccountant(): bool    { return $this->role === 'accountant'; }
    public function isTeacher(): bool       { return $this->role === 'teacher'; }
    public function isTutor(): bool         { return $this->role === 'tutor'; }
    public function isParent(): bool        { return $this->role === 'parent'; }
    public function isStudent(): bool       { return $this->role === 'student'; }

    public function canManage(): bool
    {
        return in_array($this->role, ['super_admin', 'center_manager', 'branch_manager', 'operations']);
    }

    public function canDispatch(): bool
    {
        return in_array($this->role, ['super_admin', 'center_manager']);
    }

    public function canManageBranch(): bool
    {
        return in_array($this->role, ['center_manager', 'branch_manager', 'operations']);
    }

    public function canViewFinance(): bool
    {
        return in_array($this->role, ['center_manager', 'branch_manager', 'accountant', 'super_admin']);
    }

    // ──────────────────────────────────────────────
    // MULTI-TENANT ACCESS
    // ──────────────────────────────────────────────

    /**
     * Returns all tenant IDs this user can access:
     * - Super Admin: returns null (access all)
     * - Others: home tenant + all active assignment tenants
     */
    public function accessibleTenantIds(): ?array
    {
        if ($this->isSuperAdmin()) {
            return null; // null = unrestricted
        }

        $ids = [];

        if ($this->tenant_id) {
            $ids[] = $this->tenant_id;
        }

        $assigned = $this->assignments()
                         ->active()
                         ->pluck('tenant_id')
                         ->toArray();

        return array_unique(array_merge($ids, $assigned));
    }

    /**
     * Returns all branch IDs this user can access.
     */
    public function accessibleBranchIds(): array
    {
        if ($this->isSuperAdmin()) {
            return []; // empty = unrestricted (caller should handle)
        }

        $ids = [];

        if ($this->branch_id) {
            $ids[] = $this->branch_id;
        }

        $assigned = $this->assignments()
                         ->active()
                         ->whereNotNull('branch_id')
                         ->pluck('branch_id')
                         ->toArray();

        return array_unique(array_merge($ids, $assigned));
    }

    public function canAccessTenant(int $tenantId): bool
    {
        if ($this->isSuperAdmin()) return true;
        $ids = $this->accessibleTenantIds();
        return in_array($tenantId, $ids ?? []);
    }

    // ──────────────────────────────────────────────
    // RELATIONSHIPS
    // ──────────────────────────────────────────────
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function taughtClasses(): HasMany
    {
        return $this->hasMany(ClassRoom::class, 'teacher_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ClassSession::class, 'teacher_id');
    }

    // Multi-tenant assignments
    public function assignments(): HasMany
    {
        return $this->hasMany(StaffAssignment::class);
    }

    public function activeAssignments(): HasMany
    {
        return $this->hasMany(StaffAssignment::class)->active();
    }

    // Dispatch requests (as requester/manager)
    public function dispatchRequestsMade(): HasMany
    {
        return $this->hasMany(DispatchRequest::class, 'requester_id');
    }

    // Dispatch requests (as the dispatched staff member)
    public function dispatchRequestsReceived(): HasMany
    {
        return $this->hasMany(DispatchRequest::class, 'user_id');
    }
}
