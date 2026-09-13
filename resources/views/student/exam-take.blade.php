<h1>Ujian #{{ $progress->id }}</h1>
<p>Sisa waktu: <span id="timer">{{ $secondsLeft }}</span> detik</p>
<form id="examForm" method="POST" action="{{ route('student.exam.submit', $progress->id) }}">
@csrf
<input type="hidden" name="session_token" value="{{ $progress->session_token }}">
<input type="hidden" name="score" value="0">
<button type="submit">Kumpulkan</button>
</form>
<script>
let left = {{ $secondsLeft }};
const el = document.getElementById('timer');
const t = setInterval(() => {
  left--;
  el.textContent = left;
  if (left <= 0) { clearInterval(t); document.getElementById('examForm').submit(); }
}, 1000);
</script>
