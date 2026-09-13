<h1>Dashboard {{ $student->name }}</h1>
<form method="POST" action="{{ route('student.logout') }}">@csrf<button>Keluar</button></form>
<ul>
@forelse($progress as $p)
<li><a href="{{ route('student.exam.take', [$p->id, 'token' => $p->session_token]) }}">{{ $p->examSession->name ?? $p->exam_session_id }}</a> - {{ $p->status }} - {{ $p->score ?? '-' }}</li>
@empty<li>Belum ada ujian.</li>
@endforelse
</ul>
