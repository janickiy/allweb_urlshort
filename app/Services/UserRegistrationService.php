<?php

namespace App\Services;

use App\DTO\UserData;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRegistrationService
{
    /**
     * Inject dependencies used by user registration operations.
     */
    public function __construct(private readonly UserRepository $users)
    {
    }

    /**
     * Create a public user account unless the email already exists.
     *
     * @param array<string, mixed> $data
     */
    public function createPublicUser(array $data): ?User
    {
        if (!config('settings.registration_registration')) {
            return null;
        }

        $user = $this->users->createFromDto(UserData::fromArray([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'locale' => config('app.locale'),
            'timezone' => config('settings.timezone'),
            'api_token' => Str::random(60),
        ]));

        if (!config('settings.registration_verification')) {
            $user->markEmailAsVerified();
        }

        return $user;
    }

    /**
     * Create the initial installer administrator account.
     *
     * @param array<string, mixed> $data
     */
    public function createInstallerAdmin(array $data): User
    {
        $user = $this->users->createFromDto(UserData::fromArray([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'locale' => config('app.locale'),
            'role' => 1,
            'timezone' => 'UTC',
            'api_token' => Str::random(60),
        ]));

        $user->markEmailAsVerified();

        return $user;
    }
}
