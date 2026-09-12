<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ExamSession;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamSessionFactory extends Factory
{
    protected $model = ExamSession::class;

    public function definition(): array
    {
        return [
            "name" => $this->faker->unique()->words(2, true),
            "start_time" => "07:30",
            "end_time" => "09:30",
        ];
    }
}
