<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_can_access_admin_panel(): void
    {
        $user = User::factory()->guru()->create();

        $this->actingAs($user)->get('/admin')->assertOk();
    }

    public function test_admin_can_access_admin_panel(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)->get('/admin')->assertOk();
    }

    public function test_inactive_user_cannot_access_admin_panel(): void
    {
        $user = User::factory()->admin()->inactive()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }
}
