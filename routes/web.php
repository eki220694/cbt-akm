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
    ->whereIn('module', ['majors', 'exam_sessions', 'classrooms', 'questions']);
