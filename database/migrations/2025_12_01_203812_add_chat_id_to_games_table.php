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
            $table->bigInteger('chat_id')->nullable();
        });
        Schema::table('quizzes', function (Blueprint $table): void {
            $table->boolean('for_telegram')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table): void {
            $table->dropColumn('chat_id');
        });
        Schema::table('quizzes', function (Blueprint $table): void {
            $table->dropColumn('for_telegram');
        });
    }
};
