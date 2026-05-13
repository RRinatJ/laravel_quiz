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
        if (Schema::hasIndex('games', 'games_game_id_created_at_index')) {
            Schema::table('games', function (Blueprint $table): void {
                $table->dropIndex('games_game_id_created_at_index');
            });
        }

        if (! Schema::hasIndex('games', 'games_quiz_id_created_at_index')) {
            Schema::table('games', function (Blueprint $table): void {
                $table->index(
                    ['quiz_id', 'created_at'],
                    'games_quiz_id_created_at_index'
                )->online();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasIndex('games', 'games_quiz_id_created_at_index')) {
            Schema::table('games', function (Blueprint $table): void {
                $table->dropIndex('games_quiz_id_created_at_index');
            });
        }

        if (! Schema::hasIndex('games', 'games_game_id_created_at_index')) {
            Schema::table('games', function (Blueprint $table): void {
                $table->index(
                    ['game_id', 'created_at'],
                    'games_game_id_created_at_index'
                )->online();
            });
        }
    }
};
