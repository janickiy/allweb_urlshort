<?php

namespace App\Services;

use App\DTO\UserData;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class UserSettingsService
{
    public function __construct(private readonly UserRepository $users)
    {
    }

    /**
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

    public function updatePassword(User $user, string $password): bool
    {
        $updated = $this->users->updatePassword($user, $password);
        Auth::logoutOtherDevices($password);

        return $updated;
    }

    public function regenerateApiToken(User $user): bool
    {
        return $this->users->regenerateApiToken($user);
    }

    public function deleteAccount(User $user): bool
    {
        return $this->users->forceDelete($user);
    }
}
