<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    // ponytail: PK bigint ($table->id) bukan ULID; upgrade ke ULID butuh migrasi id + backfill relasi.
    use HasFactory;

    protected $fillable = [
        "stimulus",
        "content",
        "type",
        "points",
        "answer_key",
        "options",
    ];

    protected function casts(): array
    {
        return [
            "options" => "array",
            "points" => "integer",
        ];
    }
}
