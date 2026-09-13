<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Pages\ItemAnalysis;
use App\Filament\Resources\ExamResultResource;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\Student;
use App\Models\StudentAnswer;
use App\Models\StudentExamProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamResultAnalysisTest extends TestCase
{
    use RefreshDatabase;

    public function test_rekap_muncul_dan_recalc_benar(): void
    {
        $student = Student::factory()->create();
        $session = ExamSession::factory()->create();
        $pg = Question::factory()->create(['type' => 'pg', 'answer_key' => 'A', 'points' => 2]);
        $essay = Question::factory()->create(['type' => 'essay', 'answer_key' => null, 'points' => 5]);
        $session->questions()->attach([$pg->id, $essay->id]);

        $progress = StudentExamProgress::create([
            'student_id' => $student->id,
            'exam_session_id' => $session->id,
            'session_token' => str_repeat('b', 64),
            'status' => 'finished',
            'score' => 2,
            'finished_at' => now(),
        ]);
        StudentAnswer::create(['progress_id' => $progress->id, 'question_id' => $pg->id, 'answer' => 'A', 'is_correct' => true, 'points' => 2]);
        $essayAnswer = StudentAnswer::create(['progress_id' => $progress->id, 'question_id' => $essay->id, 'answer' => 'Uraian siswa', 'is_correct' => false, 'points' => 0]);

        // Rekap read-only: terdaftar di filter sesi.
        $this->assertSame(1, StudentExamProgress::where('exam_session_id', $session->id)->count());
        $this->assertFalse(ExamResultResource::canCreate());
        $this->assertFalse(ExamResultResource::canEdit($progress));

        // Koreksi essay: set poin + benar, lalu recalc.
        $essayAnswer->update(['points' => 4, 'is_correct' => true]);
        ExamResultResource::recalcScore($progress);

        $this->assertSame(6, $progress->refresh()->score);

        // Analisis butir: % benar per soal.
        $rows = ItemAnalysis::rows($session->id)->keyBy('question_id');

        $this->assertEqualsWithDelta(1.0, (float) $rows[$pg->id]->pct_correct, 0.001);
        $this->assertEqualsWithDelta(1.0, (float) $rows[$essay->id]->pct_correct, 0.001);
        $this->assertTrue(ItemAnalysis::rows(null)->isEmpty());
    }
}
