<?php

namespace App\Http\Controllers\Installer;

use App\Services\UserRegistrationService;
use RachidLaasri\LaravelInstaller\Helpers\DatabaseManager;

class DatabaseController extends \RachidLaasri\LaravelInstaller\Controllers\DatabaseController
{
    /**
     * @var DatabaseManager
     */
    private $databaseManager;

    /**
     * Inject installer database manager and registration services.
     *
     * @param DatabaseManager $databaseManager
     */
    public function __construct(
        DatabaseManager $databaseManager,
        private readonly UserRegistrationService $registrations,
    ) {
        $this->databaseManager = $databaseManager;
    }

    /**
     * Run installer migrations and seed the first administrator account.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function database(): mixed
    {
        $response = $this->databaseManager->migrateAndSeed();

        $this->registrations->createInstallerAdmin(request()->only('name', 'email', 'password'));

        return redirect()->route('LaravelInstaller::final')
            ->with(['message' => $response]);
    }
}
