<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ["code", "name"];

    /**
     * Edge Case Guard: Memastikan kode jurusan selalu disimpan dalam huruf kapital murni
     */
    protected function code(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => strtoupper(trim($value)),
        );
    }
}
