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

    /** Acak deterministik via seed (Fisher-Yates, mt_rand). */
    private function shuffled(array $items, int $seed): array
    {
        mt_srand($seed);
        $n = count($items);
        for ($i = $n - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$items[$i], $items[$j]] = [$items[$j], $items[$i]];
        }
        mt_srand();

        return $items;
    }

    public function take(Request $request, int $progress): View|RedirectResponse
    {
        $p = $this->owned($progress);

        // Validasi session_token milik siswa ini.
        $token = (string) $request->query('token', '');
        if (! hash_equals($p->session_token, $token)) {
            abort(403, 'Token sesi tidak valid.');
        }

        if (in_array($p->status, ['finished', 'late'], true)) {
            return redirect()->route('student.exam.result', $p->id);
        }

        // Seed acak: buat sekali, pakai ulang agar urutan stabil.
        if ($p->order_seed === null) {
            $p->order_seed = random_int(1, 2147483647);
        }
        // not_started -> in_progress saat soal dibuka.
        if ($p->status === 'not_started') {
            $p->status = 'in_progress';
            $p->started_at ??= now();
        } elseif ($p->status === 'started' && $p->started_at === null) {
            $p->started_at = now();
        }
        $p->save();

        // Timer: remaining_seconds bila ada, else 60 menit dari started_at.
        $elapsed = $p->started_at ? (int) now()->diffInSeconds($p->started_at) : 0;
        $elapsed = max(0, $elapsed);
        $secondsLeft = $p->remaining_seconds ?? max(0, 3600 - $elapsed);

        $questions = $p->examSession ? $p->examSession->questions : collect();
        $ordered = $this->shuffled($questions->all(), (int) $p->order_seed);

        // Acak opsi pg/pg_kompleks per soal (seed + question_id agar stabil per soal).
        foreach ($ordered as $q) {
            $type = $q->type instanceof QuestionType ? $q->type->value : (string) $q->type;
            if (in_array($type, ['pg', 'pg_kompleks'], true) && is_array($q->options)) {
                $q->setAttribute('options', $this->shuffled(
                    array_values($q->options),
                    (int) $p->order_seed + (int) $q->id
                ));
            }
        }

        return view('student.exam-take', ['progress' => $p, 'secondsLeft' => $secondsLeft, 'questions' => collect($ordered)]);
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

        if (! in_array($p->status, ['finished', 'late'], true)) {
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

            // late bila trlambat (60 menit dari started_at).
            $elapsed = $p->started_at ? (int) now()->diffInSeconds($p->started_at) : 0;
            $late = $elapsed > 3600;
            $p->update([
                'status' => $late ? 'late' : 'finished',
                'score' => $score,
                'finished_at' => now(),
                'remaining_seconds' => max(0, 3600 - max(0, $elapsed)),
            ]);
        }

        return redirect()->route('student.exam.result', $p->id);
    }

    /** Daftar kunci: answer_keys_json dulu, fallback answer_key koma. */
    private function keyList(Question $q): ?array
    {
        $raw = $q->answer_keys_json;
        if (is_string($raw)) {
            $dec = json_decode($raw, true);
            if (is_array($dec)) {
                $raw = array_values($dec);
            }
        }
        if (is_array($raw)) {
            $list = array_values(array_filter(array_map(
                fn ($v) => is_array($v) ? json_encode(array_values($v)) : trim((string) $v),
                $raw
            ), fn ($v) => $v !== ''));
            if ($list !== []) {
                return $list;
            }
        }

        return null;
    }

    /** @return array{bool,int,?string} */
    private function grade(Question $q, mixed $given): array
    {
        $type = $q->type instanceof QuestionType ? $q->type->value : (string) $q->type;
        $key = (string) ($q->answer_key ?? '');

        if (is_array($given)) {
            $vals = array_values($given);
            $list = array_values(array_filter(array_map(
                fn ($v) => trim((string) $v),
                $vals
            ), fn ($v) => $v !== ''));
            if ($type === 'pg_kompleks') {
                sort($list);
            }
            $stored = $list === [] ? null : ($type === 'menjodohkan' ? json_encode($list) : implode(',', $list));
            $set = $list;
        } else {
            $stored = $given === null || trim((string) $given) === '' ? null : trim((string) $given);
            $set = null;
        }

        $correct = false;
        if ($type === 'pg') {
            $keys = $this->keyList($q) ?? ($key === '' ? [] : [trim($key)]);
            $correct = $stored !== null && in_array($stored, $keys, true);
        } elseif ($type === 'isian_singkat') {
            $keys = $this->keyList($q) ?? ($key === '' ? [] : [trim($key)]);
            $low = array_map(fn ($v) => mb_strtolower(trim((string) $v)), $keys);
            $correct = $stored !== null && in_array(mb_strtolower($stored), $low, true);
        } elseif ($type === 'pg_kompleks') {
            $keys = $this->keyList($q) ?? array_values(array_filter(array_map(
                fn ($v) => trim((string) $v),
                explode(',', $key)
            ), fn ($v) => $v !== ''));
            sort($keys);
            $correct = $set !== null && $set === $keys && $set !== [];
        } elseif ($type === 'menjodohkan') {
            $keys = $this->keyList($q) ?? array_values(array_filter(array_map(
                fn ($v) => trim((string) $v),
                explode(',', $key)
            ), fn ($v) => $v !== ''));
            // Urutan penting; banding case-insensitive per baris.
            $norm = fn ($v) => mb_strtolower(trim((string) $v));
            $correct = $set !== null && $set !== []
                && array_map($norm, $set) === array_map($norm, $keys);
        }
        // essay: tetap 0, butuh koreksi manual.

        return [$correct, $correct ? (int) $q->points : 0, $stored];
    }

    public function result(int $progress): View
    {
        $p = $this->owned($progress);

        return view('student.exam-result', ['progress' => $p]);
    }
}
