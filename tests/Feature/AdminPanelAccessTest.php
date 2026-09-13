<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_admin_can_access_admin_panel(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)->get('/admin')->assertOk();
    }

    public function test_guru_can_access_admin_panel(): void
    {
        $user = User::factory()->create(['role' => 'guru']);

        $this->actingAs($user)->get('/admin')->assertOk();
    }
}
