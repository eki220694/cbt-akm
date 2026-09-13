<?php

declare(strict_types=1);

namespace App\Http\Controllers\Student;

use App\Enums\QuestionType;
use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\StudentAnswer;
use App\Models\StudentExamProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ExamController extends Controller
{
    private function owned(int $id): StudentExamProgress
    {
        return StudentExamProgress::where('id', $id)
            ->where('student_id', Auth::guard('student')->id())
            ->firstOrFail();
    }

    public function take(Request $request, int $progress): View|RedirectResponse
    {
        $p = $this->owned($progress);

        // Validasi session_token milik siswa ini.
        $token = (string) $request->query('token', '');
        if (! hash_equals($p->session_token, $token)) {
            abort(403, 'Token sesi tidak valid.');
        }

        if ($p->status === 'finished') {
            return redirect()->route('student.exam.result', $p->id);
        }

        // Timer sederhana: 60 menit dari started_at.
        $secondsLeft = 3600 - (int) (now()->diffInSeconds($p->started_at ?? now(), false) > 0 ? now()->diffInSeconds($p->started_at) : 0);
        $secondsLeft = max(0, $secondsLeft);

        $questions = $p->examSession ? $p->examSession->questions : collect();

        return view('student.exam-take', ['progress' => $p, 'secondsLeft' => $secondsLeft, 'questions' => $questions]);
    }

    public function submit(Request $request, int $progress): RedirectResponse
    {
        $p = $this->owned($progress);

        $request->validate([
            'session_token' => ['required', 'string'],
            'answers' => ['nullable', 'array'],
        ]);

        if (! hash_equals($p->session_token, (string) $request->input('session_token'))) {
            abort(403, 'Token sesi tidak valid.');
        }

        if ($p->status !== 'finished') {
            $raw = $request->input('answers', []);
            $answers = is_array($raw) ? $raw : [];
            $questions = $p->examSession ? $p->examSession->questions : collect();
            $score = 0;

            foreach ($questions as $q) {
                $given = $answers[$q->id] ?? $answers[(string) $q->id] ?? null;
                [$correct, $earned, $stored] = $this->grade($q, $given);
                $score += $earned;
                StudentAnswer::updateOrCreate(
                    ['progress_id' => $p->id, 'question_id' => $q->id],
                    ['answer' => $stored, 'is_correct' => $correct, 'points' => $earned]
                );
            }

            $p->update(['status' => 'finished', 'score' => $score, 'finished_at' => now()]);
        }

        return redirect()->route('student.exam.result', $p->id);
    }

    /** @return array{bool,int,?string} */
    private function grade(Question $q, mixed $given): array
    {
        $type = $q->type instanceof QuestionType ? $q->type->value : (string) $q->type;
        $key = (string) ($q->answer_key ?? '');

        if (is_array($given)) {
            $set = array_values(array_filter(array_map(
                fn ($v) => trim((string) $v),
                $given
            ), fn ($v) => $v !== ''));
            sort($set);
            $stored = $set === [] ? null : implode(',', $set);
        } else {
            $stored = $given === null || trim((string) $given) === '' ? null : trim((string) $given);
            $set = null;
        }

        $correct = false;
        if ($type === 'pg') {
            $correct = $stored !== null && $stored === trim($key);
        } elseif ($type === 'isian_singkat') {
            $correct = $stored !== null && mb_strtolower($stored) === mb_strtolower(trim($key));
        } elseif ($type === 'pg_kompleks') {
            $keySet = array_values(array_filter(array_map(
                fn ($v) => trim((string) $v),
                explode(',', $key)
            ), fn ($v) => $v !== ''));
            sort($keySet);
            $correct = $set !== null && $set === $keySet && $set !== [];
        }
        // essay/menjodohkan: tetap 0, butuh koreksi manual.

        return [$correct, $correct ? (int) $q->points : 0, $stored];
    }

    public function result(int $progress): View
    {
        $p = $this->owned($progress);

        return view('student.exam-result', ['progress' => $p]);
    }
}
