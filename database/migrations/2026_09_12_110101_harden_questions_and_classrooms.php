<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // ponytail: tanpa ubah existing type; tambah CHECK bila butuh strict di DB.
            $table->index('type');
        });

        Schema::table('classrooms', function (Blueprint $table) {
            // ponytail: MySQL auto-index FK; eksplisit untuk sqlite/pgsql.
            $table->index('exam_session_id');
        });
    }

    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropIndex(['exam_session_id']);
        });
        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex(['type']);
        });
    }
};
