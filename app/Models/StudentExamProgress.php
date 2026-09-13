<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentExamProgress extends Model
{
    use HasFactory;

    protected $table = 'student_exam_progress';

    // status: not_started|in_progress|finished|late (+ lawas: started=selesai jalan)
    protected $fillable = [
        'student_id', 'exam_session_id', 'session_token',
        'status', 'score', 'started_at', 'finished_at',
        'order_seed', 'remaining_seconds',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'order_seed' => 'integer',
            'remaining_seconds' => 'integer',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function examSession(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class, 'progress_id');
    }
}
