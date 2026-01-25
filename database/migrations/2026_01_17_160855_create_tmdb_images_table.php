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
        Schema::create('tmdb_images', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('question_id')->index();
            $table->string('tmdb_image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tmdb_images', function (Blueprint $table): void {
            $table->dropIndex(['question_id']);
        });
        Schema::dropIfExists('tmdb_images');
    }
};
