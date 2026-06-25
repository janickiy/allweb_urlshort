<?php

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use App\Services\UserRegistrationService;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Http\RedirectResponse;

class DatabaseController extends Controller
{
    /**
     * Inject installer database manager and registration services.
     */
    public function __construct(
        private readonly ConsoleKernel $console,
        private readonly UserRegistrationService $registrations,
    ) {
    }

    /**
     * Run installer migrations and seed the first administrator account.
     */
    public function database(): RedirectResponse
    {
        $this->console->call('migrate', [
            '--seed' => true,
            '--force' => true,
        ]);

        $response = trim($this->console->output()) ?: __('Database migration and seeding completed.');

        $this->registrations->createInstallerAdmin(request()->only('name', 'email', 'password'));

        return redirect()->route('LaravelInstaller::final')
            ->with(['message' => $response]);
    }
}
