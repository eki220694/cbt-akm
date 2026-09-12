<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'content' => $this->faker->sentence(),
            'type' => 'pg',
            'points' => 1,
            'answer_key' => 'A',
            'options' => [['key' => 'A', 'value' => '4'], ['key' => 'B', 'value' => '5']],
        ];
    }
}
