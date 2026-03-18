<?php

namespace App\Traits;

use App\Models\AuditLog;

/**
 * HasAuditLog
 *
 * Add this trait to any model to automatically log created/updated/deleted events
 * to the audit_logs table via AuditLog::log().
 */
trait HasAuditLog
{
    public static function bootHasAuditLog(): void
    {
        static::created(function ($model) {
            AuditLog::log(
                event: 'created',
                model: $model,
                description: class_basename($model) . ' #' . $model->getKey() . ' created',
                newValues: $model->toArray(),
            );
        });

        static::updated(function ($model) {
            AuditLog::log(
                event: 'updated',
                model: $model,
                description: class_basename($model) . ' #' . $model->getKey() . ' updated',
                oldValues: $model->getOriginal(),
                newValues: $model->getChanges(),
            );
        });

        static::deleted(function ($model) {
            AuditLog::log(
                event: 'deleted',
                model: $model,
                description: class_basename($model) . ' #' . $model->getKey() . ' deleted',
                oldValues: $model->toArray(),
            );
        });
    }
}
