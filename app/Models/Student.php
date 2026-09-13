<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'student';

    protected $fillable = ['name', 'username', 'password', 'classroom_id'];

    protected $hidden = ['password', 'remember_token'];

    protected static function newFactory(): StudentFactory
    {
        return StudentFactory::new();
    }

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    public function examProgress(): HasMany
    {
        return $this->hasMany(StudentExamProgress::class);
    }
}
