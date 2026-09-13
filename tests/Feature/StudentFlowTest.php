<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_dan_akses_dashboard(): void
    {
        $student = Student::factory()->create(['username' => 'siswa1']);

        // Guest redirect.
        $this->get('/student/dashboard')->assertRedirect('/student/login');

        // Login sukses.
        $res = $this->post('/student/login', ['username' => 'siswa1', 'password' => 'password']);
        $res->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($student, 'student');

        // Dashboard bisa diakses.
        $this->get('/student/dashboard')->assertOk()->assertSee($student->name);

        // Login gagal.
        $this->post('/student/logout');
        $this->post('/student/login', ['username' => 'siswa1', 'password' => 'salah'])
            ->assertSessionHasErrors('username');
        $this->assertGuest('student');
    }
}
