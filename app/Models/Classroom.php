<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Classroom extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ["name", "exam_session_id"];

    public function examSession(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class, "exam_session_id");
    }
}
