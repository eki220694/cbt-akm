<h1>Hasil Ujian #{{ $progress->id }}</h1>
<p>Status: {{ $progress->status }}</p>
<p>Skor: {{ $progress->score ?? '-' }}</p>
<a href="{{ route('student.dashboard') }}">Kembali</a>
