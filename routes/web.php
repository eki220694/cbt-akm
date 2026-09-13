<?php

declare(strict_types=1);

use App\Http\Controllers\TemplateDownloadController;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Jalur khusus unduhan dokumen format Excel untuk bypass asinkronus Livewire
Route::get('/admin/templates/{module}/download', [
    TemplateDownloadController::class,
    'download',
])
    ->name('admin.templates.download')
    ->middleware(['web', Authenticate::class])
    ->whereIn('module', ['majors', 'exam_sessions', 'classrooms', 'questions', 'subjects']);

// Cetak PDF (butuh login panel admin)
use App\Http\Controllers\CetakController;

Route::middleware(['web', Authenticate::class])->prefix('cetak')->name('cetak.')->group(function () {
    Route::get('/kartu-ujian', [CetakController::class, 'kartuUjian'])->name('kartu-ujian');
    Route::get('/daftar-hadir', [CetakController::class, 'daftarHadir'])->name('daftar-hadir');
    Route::get('/berita-acara', [CetakController::class, 'beritaAcara'])->name('berita-acara');
});

use App\Http\Controllers\Student\AuthController as StudentAuth;
use App\Http\Controllers\Student\DashboardController as StudentDash;
use App\Http\Controllers\Student\ExamController as StudentExam;

Route::prefix('student')->name('student.')->group(function () {
    Route::get('/login', [StudentAuth::class, 'showLogin'])->name('login');
    Route::post('/login', [StudentAuth::class, 'login'])->name('login.attempt');
    Route::post('/logout', [StudentAuth::class, 'logout'])->name('logout')->middleware('auth:student');
    Route::get('/dashboard', [StudentDash::class, 'index'])->name('dashboard')->middleware('auth:student');
    Route::get('/exam/{progress}/take', [StudentExam::class, 'take'])->name('exam.take')->middleware('auth:student');
    Route::post('/exam/{progress}/submit', [StudentExam::class, 'submit'])->name('exam.submit')->middleware('auth:student');
    Route::get('/exam/{progress}/result', [StudentExam::class, 'result'])->name('exam.result')->middleware('auth:student');
});
