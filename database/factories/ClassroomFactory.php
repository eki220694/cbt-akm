<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\ExamSession;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassroomFactory extends Factory
{
    protected $model = Classroom::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->bothify('XII-?-#'),
            'exam_session_id' => ExamSession::factory(),
        ];
    }
}
