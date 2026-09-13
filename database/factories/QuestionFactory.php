<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\QuestionType;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'content' => $this->faker->sentence(),
            'type' => QuestionType::Pg,
            'points' => 1,
            'answer_key' => 'A',
            'options' => [['key' => 'A', 'value' => '4'], ['key' => 'B', 'value' => '5']],
        ];
    }

    public function benarSalah(): static
    {
        return $this->state(fn (array $attrs): array => [
            'type' => QuestionType::BenarSalah,
            'answer_key' => 'Benar',
            'options' => [['key' => 'Benar', 'value' => 'Benar'], ['key' => 'Salah', 'value' => 'Salah']],
        ]);
    }
}
