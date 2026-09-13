<h1>Ujian #{{ $progress->id }}</h1>
<p>Sisa waktu: <span id="timer">{{ $secondsLeft }}</span> detik</p>
<form id="examForm" method="POST" action="{{ route('student.exam.submit', $progress->id) }}">
@csrf
<input type="hidden" name="session_token" value="{{ $progress->session_token }}">
@foreach ($questions as $i => $q)
<div>
<p>{{ $i + 1 }}. {!! $q->content !!}</p>
@if ($q->stimulus)<blockquote>{!! $q->stimulus !!}</blockquote>@endif
@if ($q->type->value === 'pg')
@foreach ((array) $q->options as $opt)
<label><input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt['key'] }}"> {{ $opt['key'] }}. {{ $opt['value'] }}</label><br>
@endforeach
@elseif ($q->type->value === 'pg_kompleks')
@foreach ((array) $q->options as $opt)
<label><input type="checkbox" name="answers[{{ $q->id }}][]" value="{{ $opt['key'] }}"> {{ $opt['key'] }}. {{ $opt['value'] }}</label><br>
@endforeach
@elseif ($q->type->value === 'isian_singkat')
<input type="text" name="answers[{{ $q->id }}]">
@elseif ($q->type->value === 'essay')
<textarea name="answers[{{ $q->id }}]"></textarea>
@elseif ($q->type->value === 'menjodohkan')
@foreach ((array) $q->options as $j => $pair)
<p>{{ $pair['left'] ?? '' }}</p>
<input type="text" name="answers[{{ $q->id }}][{{ $j }}]" placeholder="Pasangan untuk baris {{ $j + 1 }}">
@endforeach
@endif
</div>
@endforeach
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
