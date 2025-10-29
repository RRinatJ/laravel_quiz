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
        Schema::table('quizzes', function (Blueprint $table): void {
            $table->boolean('fifty_fifty_hint')->default(false);
            $table->boolean('can_skip')->default(false);
        });
        Schema::table('games', function (Blueprint $table): void {
            $table->boolean('fifty_fifty_hint')->default(false);
            $table->boolean('can_skip')->default(false);
        });
        Schema::table('game_steps', function (Blueprint $table): void {
            $table->boolean('fifty_fifty_hint')->default(false);
            $table->boolean('can_skip')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table): void {
            $table->dropColumn('fifty_fifty_hint');
            $table->dropColumn('can_skip');
        });
        Schema::table('games', function (Blueprint $table): void {
            $table->dropColumn('fifty_fifty_hint');
            $table->dropColumn('can_skip');
        });
        Schema::table('game_steps', function (Blueprint $table): void {
            $table->dropColumn('fifty_fifty_hint');
            $table->dropColumn('can_skip');
        });
    }
};
