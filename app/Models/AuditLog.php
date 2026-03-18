<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tenant_id', 'user_id', 'event',
        'auditable_type', 'auditable_id',
        'old_values', 'new_values',
        'ip_address', 'user_agent', 'description',
    ];

    protected $casts = [
        'old_values'  => 'array',
        'new_values'  => 'array',
        'created_at'  => 'datetime',
    ];

    /** Quick helper to write a log entry from anywhere */
    public static function log(
        string $event,
        ?object $model = null,
        string $description = '',
        ?array $oldValues = null,
        ?array $newValues = null,
    ): static {
        return static::create([
            'tenant_id'      => auth()->user()?->tenant_id,
            'user_id'        => auth()->id(),
            'event'          => $event,
            'auditable_type' => $model ? get_class($model) : null,
            'auditable_id'   => $model?->id,
            'old_values'     => $oldValues,
            'new_values'     => $newValues,
            'ip_address'     => request()->ip(),
            'user_agent'     => substr(request()->userAgent() ?? '', 0, 200),
            'description'    => $description,
        ]);
    }

    public function user(): BelongsTo  { return $this->belongsTo(User::class); }
    public function tenant(): BelongsTo{ return $this->belongsTo(Tenant::class); }
}
