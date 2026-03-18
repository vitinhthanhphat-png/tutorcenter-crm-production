<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassRoom extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'classes'; // 'class' is a reserved PHP keyword

    protected $fillable = [
        'tenant_id', 'branch_id', 'course_id', 'name',
        'teacher_id', 'tutor_id', 'room_name',
        'schedule_rule', 'start_date', 'end_date',
        'max_students', 'status', 'notes',
    ];

    protected $casts = [
        'schedule_rule' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // --- Scopes ---
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForTeacher($query, int $teacherId)
    {
        return $query->where('teacher_id', $teacherId)
                     ->orWhere('tutor_id', $teacherId);
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

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ClassSession::class, 'class_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'class_id');
    }

    public function students()
    {
        return $this->hasManyThrough(Student::class, Enrollment::class, 'class_id', 'id', 'id', 'student_id');
    }
}
