<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\QuestionType;
use Database\Factories\QuestionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    // ponytail: PK bigint ($table->id) bukan ULID; upgrade ke ULID butuh migrasi id + backfill relasi.
    use HasFactory;

    protected $fillable = [
        'stimulus',
        'content',
        'type',
        'subject_id',
        'points',
        'answer_key',
        'answer_keys_json',
        'options',
    ];

    protected static function newFactory(): QuestionFactory
    {
        return QuestionFactory::new();
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function examSessions(): BelongsToMany
    {
        return $this->belongsToMany(ExamSession::class);
    }

    public function studentAnswers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class);
    }

    protected function casts(): array
    {
        return [
            'type' => QuestionType::class,
            'options' => 'array',
            'answer_keys_json' => 'array',
            'points' => 'integer',
        ];
    }
}
