<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSession extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ["name", "start_time", "end_time"];

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }
}
