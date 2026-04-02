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
        Schema::table('games', function (Blueprint $table): void {
            $table->index('quiz_id')->online();
            $table->index(['game_id', 'created_at'])->online();
        });

        Schema::table('game_steps', function (Blueprint $table): void {
            $table->index('game_id')->online();
            $table->index(['game_id', 'created_at'])->online();
        });

        Schema::table('answers', function (Blueprint $table): void {
            $table->index('question_id')->online();
        });

        Schema::table('quizzes', function (Blueprint $table): void {
            $table->index('is_work')->online();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table): void {
            $table->dropIndex(['quiz_id']);
            $table->dropIndex(['game_id', 'created_at']);
        });

        Schema::table('game_steps', function (Blueprint $table): void {
            $table->dropIndex(['game_id']);
            $table->dropIndex(['game_id', 'created_at']);
        });

        Schema::table('answers', function (Blueprint $table): void {
            $table->dropIndex(['question_id']);
        });

        Schema::table('quizzes', function (Blueprint $table): void {
            $table->dropIndex(['is_work']);
        });
    }
};
