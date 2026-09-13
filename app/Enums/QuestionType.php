<?php

declare(strict_types=1);

namespace App\Enums;

enum QuestionType: string
{
    case Pg = 'pg';
    case PgKompleks = 'pg_kompleks';
    case IsianSingkat = 'isian_singkat';
    case Essay = 'essay';
    case Menjodohkan = 'menjodohkan';
    case BenarSalah = 'benar_salah';

    public function label(): string
    {
        return match ($this) {
            self::Pg => 'Pilihan Ganda',
            self::PgKompleks => 'Pilihan Ganda Kompleks',
            self::IsianSingkat => 'Isian Singkat',
            self::Essay => 'Essay/Uraian',
            self::Menjodohkan => 'Menjodohkan',
            self::BenarSalah => 'Benar Salah',
        };
    }
}
