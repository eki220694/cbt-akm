<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ExamSession;
use App\Models\Question;
use App\Models\Student;
use App\Models\StudentAnswer;
use App\Models\StudentExamProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamSubmitTest extends TestCase
{
    use RefreshDatabase;

    public function test_submit_nilai_pg_otomatis(): void
    {
        $student = Student::factory()->create();
        $session = ExamSession::factory()->create();
        $q1 = Question::factory()->create(['type' => 'pg', 'answer_key' => 'A', 'points' => 2]);
        $q2 = Question::factory()->create(['type' => 'pg', 'answer_key' => 'B', 'points' => 3]);
        $session->questions()->attach([$q1->id, $q2->id]);

        $progress = StudentExamProgress::create([
            'student_id' => $student->id,
            'exam_session_id' => $session->id,
            'session_token' => str_repeat('a', 64),
            'status' => 'started',
            'started_at' => now(),
        ]);

        $this->actingAs($student, 'student')->post(route('student.exam.submit', $progress->id), [
            'session_token' => str_repeat('a', 64),
            'answers' => [$q1->id => 'A', $q2->id => 'C'],
        ])->assertRedirect(route('student.exam.result', $progress->id));

        $progress->refresh();
        $this->assertSame('finished', $progress->status);
        $this->assertSame(2, $progress->score);
        $this->assertSame(2, StudentAnswer::where('progress_id', $progress->id)->count());
        $this->assertDatabaseHas('student_answers', [
            'progress_id' => $progress->id, 'question_id' => $q1->id,
            'answer' => 'A', 'is_correct' => true, 'points' => 2,
        ]);
        $this->assertDatabaseHas('student_answers', [
            'progress_id' => $progress->id, 'question_id' => $q2->id,
            'answer' => 'C', 'is_correct' => false, 'points' => 0,
        ]);
    }
}
