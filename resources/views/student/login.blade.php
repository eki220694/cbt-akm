<h1>Login Siswa</h1>
<form method="POST" action="{{ route('student.login.attempt') }}">
@csrf
<input name="username" value="{{ old('username') }}" placeholder="Username" required>
@error('username')<p>{{ $message }}</p>@enderror
<input type="password" name="password" placeholder="Password" required>
<button type="submit">Masuk</button>
</form>
