<?php

declare(strict_types=1);

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
        Schema::create('games', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignId('current_question_id')->nullable()->constrained('questions')->nullOnDelete();
            $table->foreignId('quiz_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('correct_count');
            $table->json('question_row');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
