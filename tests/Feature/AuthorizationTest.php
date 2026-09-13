<?php

namespace Tests\Feature;

use App\Filament\Resources\ExamSessionResource;
use App\Filament\Resources\QuestionResource;
use App\Filament\Resources\SubjectResource;
use App\Filament\Resources\UserResource;
use App\Models\ExamSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_ditolak_hapus_sesi(): void
    {
        $guru = User::factory()->guru()->create();
        $session = ExamSession::factory()->create();

        $this->actingAs($guru);

        $this->assertFalse(ExamSessionResource::canDelete($session));
        $this->assertFalse(ExamSessionResource::canCreate());
    }

    public function test_nonaktif_ditolak_panel(): void
    {
        $user = User::factory()->admin()->inactive()->create();

        $this->actingAs($user);

        $this->assertFalse($user->canAccessPanel(filament()->getPanel('admin')));
        $this->assertFalse(UserResource::canAccess());
        $this->assertFalse(SubjectResource::canAccess());
    }

    public function test_guru_bisa_kelola_bank_soal_tapi_bukan_user(): void
    {
        $guru = User::factory()->guru()->create();

        $this->actingAs($guru);

        $this->assertTrue(SubjectResource::canCreate());
        $this->assertTrue(QuestionResource::canCreate());
        $this->assertFalse(UserResource::canAccess());
    }

    public function test_admin_full(): void
    {
        $admin = User::factory()->admin()->create();
        $session = ExamSession::factory()->create();

        $this->actingAs($admin);

        $this->assertTrue(SubjectResource::canCreate());
        $this->assertTrue(ExamSessionResource::canDelete($session));
        $this->assertTrue(UserResource::canAccess());
    }
}
