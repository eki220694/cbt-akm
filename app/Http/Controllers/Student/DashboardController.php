<?php

declare(strict_types=1);

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $student = Auth::guard('student')->user();

        $progress = $student->examProgress()->with('examSession')->latest()->get();

        return view('student.dashboard', compact('student', 'progress'));
    }
}
