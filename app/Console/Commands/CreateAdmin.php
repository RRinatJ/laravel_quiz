<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

final class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user with admin role';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userData = $this->getUserData();
        User::query()->create($userData);
        $this->info('Admin user created successfully.');

        return self::SUCCESS;
    }

    /**
     * @return array{'name': string, 'email': string, 'password': string, 'role': UserRole}
     */
    private function getUserData(): array
    {
        return [
            'name' => text(
                label: 'Name',
                required: true,
            ),

            'email' => text(
                label: 'Email address',
                required: true,
                validate: fn (string $email): ?string => match (true) {
                    ! filter_var($email, FILTER_VALIDATE_EMAIL) => 'The email address is not valid',
                    User::query()->where('email', $email)->exists() => 'The email address is already taken',
                    default => null,
                },
            ),

            'password' => Hash::make(password(
                label: 'Password',
                required: true,
            )),

            'role' => UserRole::ADMIN,
        ];
    }
}
