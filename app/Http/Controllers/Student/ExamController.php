<?php

declare(strict_types=1);

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
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

        return view('student.exam-take', ['progress' => $p, 'secondsLeft' => $secondsLeft]);
    }

    public function submit(Request $request, int $progress): RedirectResponse
    {
        $p = $this->owned($progress);

        $request->validate(['session_token' => ['required', 'string']]);

        if (! hash_equals($p->session_token, (string) $request->input('session_token'))) {
            abort(403, 'Token sesi tidak valid.');
        }

        if ($p->status !== 'finished') {
            $p->update([
                'status' => 'finished',
                'score' => (int) $request->input('score', 0), // ponytail: skor dummy; hitung dari jawaban saat bank soal siap.
                'finished_at' => now(),
            ]);
        }

        return redirect()->route('student.exam.result', $p->id);
    }

    public function result(int $progress): View
    {
        $p = $this->owned($progress);

        return view('student.exam-result', ['progress' => $p]);
    }
}
