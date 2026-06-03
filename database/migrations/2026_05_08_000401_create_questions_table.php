<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->text('stimulus')->nullable(); // Untuk teks bacaan atau gambar stimulus AKM
            $table->text('content'); // Isi soal/pertanyaan
        
            // Jenis soal: pg, essay, pg_kompleks, isian_singkat, menjodohkan
            $table->string('type'); 
        
            // Kolom JSON untuk menyimpan opsi (pilihan) dan kunci jawaban secara fleksibel
            $table->json('options')->nullable(); 
            $table->json('answer_key')->nullable(); 
        
            $table->integer('points')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
