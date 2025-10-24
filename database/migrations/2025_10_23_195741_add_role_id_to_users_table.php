<?php

declare(strict_types=1);

use App\Enums\UserRole;
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
        Schema::table('users', function (Blueprint $table): void {
            $table->unsignedBigInteger('role')->default(UserRole::ADMIN->value)->after('id')->index();
        });
        Schema::table('games', function (Blueprint $table): void {
            $table->unsignedBiginteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex(['role']);
            $table->dropColumn('role');
        });
        Schema::table('games', function (Blueprint $table): void {
            $table->dropColumn('user_id');
        });
    }
};
