<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

final class UpdateUserRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-user-role';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update user role';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $user = $this->getUser();
        if (is_null($user)) {
            $this->warn('No user with this email address was found');

            return self::FAILURE;
        }

        $role = $this->getRole();
        if (is_null($role)) {
            $this->warn('The user role is incorrect');

            return self::FAILURE;
        }

        if ($user->role === $role) {
            $this->warn('This role has already been assigned to the user');

            return self::FAILURE;
        }

        $user->role = $role;
        $user->save();

        $this->info('User updated successfully.');

        return self::SUCCESS;
    }

    private function getUser(): ?User
    {
        $email = text(
            label: 'Email address',
            required: true,
            validate: fn (string $email): ?string => match (true) {
                ! filter_var($email, FILTER_VALIDATE_EMAIL) => 'The email address is not valid',
                default => null,
            },
        );

        return User::query()
            ->where('email', $email)
            ->first();
    }

    private function getRole(): ?UserRole
    {
        $roleLabels = collect(UserRole::cases())->map(fn ($case): string => $case->label()
        )->toArray();

        $label = select(
            label: 'Which role should be assigned?',
            options: $roleLabels,
            default: 'User'
        );

        return UserRole::fromLabel($label);
    }
}
