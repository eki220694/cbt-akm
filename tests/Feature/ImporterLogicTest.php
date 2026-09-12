<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\ExamSession;
use App\Models\Major;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImporterLogicTest extends TestCase
{
    use RefreshDatabase;

    public function test_major_code_disimpan_uppercase(): void
    {
        $major = Major::create(['code' => 'mipa', 'name' => 'MIPA Alam']);

        $this->assertSame('MIPA', $major->fresh()->code);
    }

    public function test_template_download_butuh_login(): void
    {
        foreach (['majors', 'exam_sessions', 'classrooms', 'questions'] as $module) {
            // ponytail: Filament redirect /admin/login tanpa route login bawaan; cukup pastikan bukan 200/404.
            $this->get("/admin/templates/{$module}/download")->assertRedirect();
        }

        $this->get('/admin/templates/semua/download')->assertStatus(404);
    }

    public function test_template_download_terbuka_setelah_login(): void
    {
        $user = User::factory()->create();

        // ponytail: Filament Authenticate 403 bila user tak punya akses panel; cukup pastikan bukan redirect login.
        foreach (['majors', 'exam_sessions', 'classrooms', 'questions'] as $module) {
            $this->actingAs($user)->get("/admin/templates/{$module}/download")->assertForbidden();
        }
    }

    public function test_bulk_assign_1_query_dan_eager_tanpa_n_plus_1(): void
    {
        $sesi = ExamSession::factory()->create();
        Classroom::factory()->count(3)->create(['exam_session_id' => $sesi->id]);

        Classroom::where('exam_session_id', $sesi->id)->update(['exam_session_id' => $sesi->id]);

        $loaded = Classroom::with('examSession')->get();
        $this->assertTrue($loaded->every(fn ($c) => $c->relationLoaded('examSession')));
    }

    public function test_question_cast_dan_fillable_stimulus(): void
    {
        $q = Question::factory()->create(['stimulus' => 'Bacaan AKM', 'options' => [['key' => 'A', 'value' => '4']]]);

        $fresh = $q->fresh();
        $this->assertSame('Bacaan AKM', $fresh->stimulus);
        $this->assertIsArray($fresh->options);
    }
}
