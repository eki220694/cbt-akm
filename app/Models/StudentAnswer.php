<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAnswer extends Model
{
    protected $fillable = [
        'progress_id', 'question_id', 'answer', 'is_correct', 'points',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'points' => 'integer',
        ];
    }

    public function progress(): BelongsTo
    {
        return $this->belongsTo(StudentExamProgress::class, 'progress_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
