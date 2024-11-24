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
         Schema::create('tpk_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question_text')->nullable();
            $table->string('question_image')->nullable();
            $table->enum('difficulty', ['low', 'intermediate', 'advance']);
            $table->json('options'); // Menyimpan opsi jawaban sebagai JSON
            $table->unsignedTinyInteger('is_correct'); // Indeks jawaban benar
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tpk_questions');
    }
};
