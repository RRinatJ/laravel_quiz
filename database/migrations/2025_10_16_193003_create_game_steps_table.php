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
        Schema::create('game_steps', function (Blueprint $table): void {
            $table->id();
            $table->foreignUlid('game_id')->constrained();
            $table->foreignId('question_id')->constrained();
            $table->unsignedBiginteger('answer_id')->nullable();
            $table->foreign('answer_id')->references('id')
                ->on('answers');
            $table->boolean('is_correct');
            $table->boolean('times_out');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_steps');
    }
};
