<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ExamSession;
use App\Models\Question;
use App\Models\Student;
use App\Models\StudentExamProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ExamShuffleMultiKeyTest extends TestCase
{
    use RefreshDatabase;

    private function progressWith(int $seed = 12345): array
    {
        $student = Student::factory()->create();
        $session = ExamSession::factory()->create();
        $qs = [];
        foreach (['A', 'B', 'C', 'D', 'E'] as $i => $k) {
            $qs[] = Question::factory()->create([
                'type' => 'pg', 'answer_key' => $k, 'points' => 1,
                'options' => [['key' => 'A', 'value' => 'a'], ['key' => 'B', 'value' => 'b'], ['key' => 'C', 'value' => 'c']],
            ]);
        }
        $session->questions()->attach(collect($qs)->pluck('id')->all());
        $p = StudentExamProgress::create([
            'student_id' => $student->id,
            'exam_session_id' => $session->id,
            'session_token' => str_repeat('b', 64),
            'status' => 'in_progress',
            'started_at' => now(),
            'order_seed' => $seed,
        ]);

        return [$student, $p, $qs];
    }

    public function test_acak_deterministik_urutan_soal_dan_opsi(): void
    {
        [$student, $p] = $this->progressWith();

        $grab = function () use ($student, $p) {
            $res = $this->actingAs($student, 'student')
                ->get(route('student.exam.take', $p->id).'?token='.str_repeat('b', 64));
            $res->assertOk();
            /** @var Collection $qs */
            $qs = $res->viewData('questions');

            return [$qs->pluck('id')->all(), $qs->map(fn ($q) => collect($q->options)->pluck('key')->all())->all()];
        };

        [$o1, $opts1] = $grab();
        [$o2, $opts2] = $grab();
        $this->assertSame($o1, $o2, 'seed sama -> urutan sama');
        $this->assertSame($opts1, $opts2, 'seed sama -> opsi sama');

        // seed beda -> kemungkinan besar urutan beda (5 soal, cek minimal salah satu beda)
        $p->update(['order_seed' => 99999]);
        [$o3] = $grab();
        $this->assertNotSame($o1, $o3);

        // seed disimpan bila kosong
        $s2 = Student::factory()->create();
        $sess = ExamSession::first();
        $p2 = StudentExamProgress::create([
            'student_id' => $s2->id, 'exam_session_id' => $sess->id,
            'session_token' => str_repeat('c', 64), 'status' => 'not_started',
        ]);
        $this->actingAs($s2, 'student')
            ->get(route('student.exam.take', $p2->id).'?token='.str_repeat('c', 64))->assertOk();
        $this->assertNotNull($p2->refresh()->order_seed);
        $this->assertSame('in_progress', $p2->status);
    }

    public function test_nilai_multi_kunci_pg_kompleks_menjodohkan_isian(): void
    {
        $student = Student::factory()->create();
        $session = ExamSession::factory()->create();
        $qPg = Question::factory()->create([
            'type' => 'pg', 'answer_key' => 'A', 'points' => 2,
            'answer_keys_json' => ['A', 'B'],
        ]);
        $qKom = Question::factory()->create([
            'type' => 'pg_kompleks', 'answer_key' => 'A,B', 'points' => 3,
            'answer_keys_json' => ['A', 'B'],
        ]);
        $qIsi = Question::factory()->create([
            'type' => 'isian_singkat', 'answer_key' => 'Jakarta', 'points' => 1,
            'answer_keys_json' => ['jakarta', 'DKI Jakarta'],
        ]);
        $qJod = Question::factory()->create([
            'type' => 'menjodohkan', 'answer_key' => 'X,Y', 'points' => 4,
            'answer_keys_json' => ['X', 'Y'],
        ]);
        $session->questions()->attach([$qPg->id, $qKom->id, $qIsi->id, $qJod->id]);
        $p = StudentExamProgress::create([
            'student_id' => $student->id, 'exam_session_id' => $session->id,
            'session_token' => str_repeat('d', 64), 'status' => 'in_progress',
            'started_at' => now(),
        ]);

        // jawaban benar semua (isian beda case, kompleks urutan beda, jodoh urutan benar)
        $this->actingAs($student, 'student')->post(route('student.exam.submit', $p->id), [
            'session_token' => str_repeat('d', 64),
            'answers' => [
                $qPg->id => 'B',
                $qKom->id => ['B', 'A'],
                $qIsi->id => 'JAKARTA',
                $qJod->id => ['X', 'Y'],
            ],
        ])->assertRedirect(route('student.exam.result', $p->id));
        $this->assertSame(10, $p->refresh()->score);

        // fallback koma + jodoh salah urutan -> 0 utk jodoh
        $p2 = StudentExamProgress::create([
            'student_id' => $student->id, 'exam_session_id' => $session->id,
            'session_token' => str_repeat('e', 64), 'status' => 'in_progress',
            'started_at' => now(),
        ]);
        $qKom->update(['answer_keys_json' => null]); // fallback parse koma answer_key
        $qJod->update(['answer_keys_json' => null]);
        $this->actingAs($student, 'student')->post(route('student.exam.submit', $p2->id), [
            'session_token' => str_repeat('e', 64),
            'answers' => [
                $qPg->id => 'B',
                $qKom->id => ['A', 'B'],
                $qIsi->id => 'dki jakarta',
                $qJod->id => ['Y', 'X'], // urutan salah
            ],
        ]);
        $this->assertSame(2 + 3 + 1 + 0, $p2->refresh()->score);
    }

    public function test_login_nisn(): void
    {
        $s = Student::factory()->create(['username' => 'budi', 'nisn' => '1234567890']);
        $this->post('/student/login', ['username' => '1234567890', 'password' => 'password'])
            ->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($s, 'student');
    }
}
