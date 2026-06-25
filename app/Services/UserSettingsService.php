<?php

namespace App\Services;

use App\DTO\UserData;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class UserSettingsService
{
    /**
     * Inject dependencies used by user settings operations.
     */
    public function __construct(private readonly UserRepository $users)
    {
    }

    /**
     * Update profile fields for a user.
     *
     * @param array<string, mixed> $input
     */
    public function updateProfile(User $user, array $input, bool $admin = false): bool
    {
        $attributes = [
            'name' => $input['name'] ?? $user->name,
            'email' => $input['email'] ?? $user->email,
            'timezone' => $input['timezone'] ?? $user->timezone,
        ];

        if ($admin) {
            $attributes['role'] = $input['role'] ?? $user->role;
        }

        return $this->users->updateFromDto($user->id, UserData::fromArray($attributes));
    }

    /**
     * Update the password for a user.
     */
    public function updatePassword(User $user, string $password): bool
    {
        $updated = $this->users->updatePassword($user, $password);
        Auth::setUser($user->fresh());
        Auth::logoutOtherDevices($password);

        return $updated;
    }

    /**
     * Regenerate the API token for a user.
     */
    public function regenerateApiToken(User $user): bool
    {
        return $this->users->regenerateApiToken($user);
    }

    /**
     * Delete a user account.
     */
    public function deleteAccount(User $user): bool
    {
        return $this->users->forceDelete($user);
    }
}
