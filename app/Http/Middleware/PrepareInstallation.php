<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PrepareInstallation
{
    /**
     * Redirect uninstalled projects to the installer before database-backed middleware runs.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->isInstalled()) {
            if ($request->is('install*') && ! $request->is('install/complete')) {
                return redirect()->route('home');
            }

            return $next($request);
        }

        $this->ensureTemporaryApplicationKey();

        if ($request->is('install/complete')) {
            return redirect()->route('install.start');
        }

        if ($request->is('install*')) {
            return $next($request);
        }

        return redirect()->route('install.start');
    }

    /**
     * Determine whether the application has completed installation.
     */
    private function isInstalled(): bool
    {
        return file_exists(base_path('.env')) && file_exists(storage_path('installed'));
    }

    /**
     * Ensure sessions and CSRF can work before a permanent app key is generated.
     */
    private function ensureTemporaryApplicationKey(): void
    {
        if (filled(config('app.key'))) {
            return;
        }

        $keyPath = storage_path('app/install.key');
        $key = file_exists($keyPath)
            ? trim((string) file_get_contents($keyPath))
            : 'base64:'.base64_encode(random_bytes(32));

        config(['app.key' => $key]);

        if (! file_exists($keyPath)) {
            @file_put_contents($keyPath, $key);
        }
    }
}
