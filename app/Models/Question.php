<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        "content",
        "type",
        "points",
        "answer_key",
        "options",
    ];

    protected function casts(): array
    {
        return [
            "options" => "json",
            "points" => "integer",
        ];
    }
}
