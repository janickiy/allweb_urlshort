<?php

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RachidLaasri\LaravelInstaller\Helpers\DatabaseManager;

class DatabaseController extends \RachidLaasri\LaravelInstaller\Controllers\DatabaseController
{
    /**
     * @var DatabaseManager
     */
    private $databaseManager;

    /**
     * @param DatabaseManager $databaseManager
     */
    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }

    /**
     * Migrate and seed the database.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function database()
    {
        $response = $this->databaseManager->migrateAndSeed();

        $user = User::create([
            'name' => request()->input('name'),
            'email' => request()->input('email'),
            'password' => Hash::make(request()->input('password')),
            'locale' => config('app.locale'),
            'role' => 1,
            'timezone' => 'UTC',
            'api_token' => Str::random(60)
        ]);
        $user->markEmailAsVerified();

        return redirect()->route('LaravelInstaller::final')
            ->with(['message' => $response]);
    }
}
