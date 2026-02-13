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
        Schema::table('tmdb_images', function (Blueprint $table): void {
            $table->foreignId('question_id')->nullable()->change();
            $table->foreignId('answer_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tmdb_images', function (Blueprint $table): void {
            $table->foreignId('question_id')->nullable(false)->change();
            $table->dropIndex(['answer_id']);
            $table->dropColumn('answer_id');
        });
    }
};
