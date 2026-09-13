<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentExamProgress extends Model
{
    use HasFactory;

    protected $table = 'student_exam_progress';

    protected $fillable = [
        'student_id', 'exam_session_id', 'session_token',
        'status', 'score', 'started_at', 'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
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
}
