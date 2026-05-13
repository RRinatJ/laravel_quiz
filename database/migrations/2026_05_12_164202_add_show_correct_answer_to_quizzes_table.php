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
            $table->boolean('show_correct_answer')->default(false)->after('ignore_error');
        });
        Schema::table('games', function (Blueprint $table): void {
            $table->boolean('show_correct_answer')->default(false)->after('can_skip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table): void {
            $table->dropColumn('show_correct_answer');
        });
        Schema::table('games', function (Blueprint $table): void {
            $table->dropColumn('show_correct_answer');
        });
    }
};
