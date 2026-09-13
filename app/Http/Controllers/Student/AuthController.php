<?php

declare(strict_types=1);

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('student.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $cred = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('student')->attempt($cred, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('student.dashboard'));
        }

        return back()->withErrors(['username' => 'Kredensial salah.'])->onlyInput('username');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login');
    }
}
