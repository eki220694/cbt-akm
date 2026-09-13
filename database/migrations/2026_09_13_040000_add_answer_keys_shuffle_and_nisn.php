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
            $table->json('answer_keys_json')->nullable(); // multi-kunci: list/set per tipe
        });

        Schema::table('student_exam_progress', function (Blueprint $table) {
            // status: kolom string, dukung not_started|in_progress|finished|late (+ lawas started)
            $table->bigInteger('order_seed')->nullable();
            $table->integer('remaining_seconds')->nullable();
        });

        Schema::table('students', function (Blueprint $table) {
            // classroom_id FK nullable sudah ada; tambah nisn saja
            $table->string('nisn', 50)->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['nisn']);
            $table->dropColumn('nisn');
        });
        Schema::table('student_exam_progress', function (Blueprint $table) {
            $table->dropColumn(['order_seed', 'remaining_seconds']);
        });
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('answer_keys_json');
        });
    }
};
