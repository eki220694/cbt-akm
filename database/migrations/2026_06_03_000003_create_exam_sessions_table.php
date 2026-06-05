<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("exam_sessions", function (Blueprint $table) {
            $table->ulid("id")->primary();
            $table->string("name")->unique();
            $table->time("start_time");
            $table->time("end_time");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("exam_sessions");
    }
};
