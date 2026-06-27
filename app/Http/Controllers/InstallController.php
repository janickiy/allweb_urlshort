<?php

namespace App\Http\Controllers;

use App\Http\Requests\Install\AdminRequest;
use App\Http\Requests\Install\DatabaseRequest;
use App\Models\User;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class InstallController extends Controller
{
    /**
     * Show the first installer step.
     */
    public function index(): View
    {
        return view('install.start');
    }

    /**
     * Show PHP and extension requirements.
     */
    public function requirements(): View
    {
        $requirements = $this->requirementsList();
        $allLoaded = $this->allChecksPassed($requirements);

        return view('install.requirements', compact('requirements', 'allLoaded'));
    }

    /**
     * Show writable path checks after system requirements pass.
     */
    public function permissions(): View|RedirectResponse
    {
        if (! $this->allChecksPassed($this->requirementsList())) {
            return redirect()->route('install.requirements');
        }

        $permissions = $this->permissionsList();
        $allGranted = $this->allChecksPassed($permissions);

        return view('install.permissions', compact('permissions', 'allGranted'));
    }

    /**
     * Show the database connection form.
     */
    public function database(): View|RedirectResponse
    {
        if (! $this->allChecksPassed($this->requirementsList())) {
            return redirect()->route('install.requirements');
        }

        if (! $this->allChecksPassed($this->permissionsList())) {
            return redirect()->route('install.permissions');
        }

        return view('install.database');
    }

    /**
     * Validate the database connection and store credentials for the next step.
     */
    public function installation(DatabaseRequest $request): RedirectResponse
    {
        if (! $this->allChecksPassed($this->requirementsList())) {
            return redirect()->route('install.requirements');
        }

        if (! $this->allChecksPassed($this->permissionsList())) {
            return redirect()->route('install.permissions');
        }

        $credentials = [
            'host' => (string) $request->input('db_host'),
            'port' => (int) $request->input('db_port'),
            'database' => (string) $request->input('db_database'),
            'username' => (string) $request->input('db_username'),
            'password' => (string) $request->input('db_password', ''),
        ];

        if (! $this->databaseCredentialsAreValid($credentials)) {
            return redirect()
                ->route('install.database')
                ->withInput()
                ->withErrors(__('install.str.connection_to_database_cannot_be_established'));
        }

        Session::put('install.db_credentials', $credentials);

        return redirect()->route('install.admin');
    }

    /**
     * Show the first administrator creation form.
     */
    public function admin(): View|RedirectResponse
    {
        if (! Session::has('install.db_credentials')) {
            return redirect()->route('install.database');
        }

        return view('install.admin');
    }

    /**
     * Write configuration, run migrations, seed defaults, and create the first administrator.
     */
    public function install(AdminRequest $request): RedirectResponse
    {
        $previousEnvironment = $this->readEnvironmentFile();

        try {
            $credentials = Session::pull('install.db_credentials');

            if (! is_array($credentials)) {
                return redirect()->route('install.database');
            }

            $locale = $this->installLocale();

            $this->writeEnvironment($credentials, $locale);
            $this->reloadEnvironment();
            $this->setDatabaseCredentials($credentials);

            Config::set('app.locale', $locale);
            Config::set('app.name', 'ShortLink Pro');
            app()->setLocale($locale);

            Artisan::call('config:clear');
            Artisan::call('key:generate', ['--force' => true]);
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\InitialDataSeeder',
                '--force' => true,
            ]);
            Artisan::call('storage:link', ['--force' => true]);

            $this->createAdministrator($request);
            $this->markInstalled();

            return redirect()
                ->route('install.complete')
                ->withCookie(Cookie::forever('locale', $locale));
        } catch (Throwable $exception) {
            $this->restoreEnvironment($previousEnvironment);
            Log::error($exception->getMessage(), ['exception' => $exception]);

            return redirect()->route('install.error');
        }
    }

    /**
     * Show the successful installation page.
     */
    public function complete(): View
    {
        return view('install.complete');
    }

    /**
     * Show the installation error page.
     */
    public function error(): View
    {
        return view('install.error');
    }

    /**
     * Handle installer AJAX actions, including locale changes.
     */
    public function ajax(Request $request): JsonResponse
    {
        if ($request->input('action') === 'change_locale') {
            $locale = (string) $request->input('locale');

            if ($this->isSupportedLocale($locale)) {
                Session::put('install.locale', $locale);
                app()->setLocale($locale);
                Cookie::queue(Cookie::forever('install_locale', $locale));
                Cookie::queue(Cookie::forever('locale', $locale));
            }

            return response()->json(['result' => true]);
        }

        return response()->json(['result' => false]);
    }

    /**
     * Return PHP and extension requirements for the project.
     *
     * @return array<string, bool>
     */
    private function requirementsList(): array
    {
        return [
            'PHP Version (>= 8.4.0)' => version_compare(PHP_VERSION, '8.4.0', '>='),
            'BCMath Extension' => extension_loaded('bcmath'),
            'Ctype Extension' => extension_loaded('ctype'),
            'cURL Extension' => extension_loaded('curl'),
            'DOM Extension' => extension_loaded('dom'),
            'Fileinfo Extension' => extension_loaded('fileinfo'),
            'GD Extension' => extension_loaded('gd'),
            'JSON Extension' => extension_loaded('json'),
            'Mbstring Extension' => extension_loaded('mbstring'),
            'OpenSSL Extension' => extension_loaded('openssl'),
            'PDO Extension' => extension_loaded('PDO'),
            'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
            'Tokenizer Extension' => extension_loaded('tokenizer'),
            'XML Extension' => extension_loaded('xml'),
        ];
    }

    /**
     * Return directories and files that must be writable during installation.
     *
     * @return array<string, bool>
     */
    private function permissionsList(): array
    {
        return [
            'Project root (.env)' => is_writable(base_path()),
            'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
            'public/uploads/brand' => is_writable(public_path('uploads/brand')),
            'storage/app' => is_writable(storage_path('app')),
            'storage/framework/cache' => is_writable(storage_path('framework/cache')),
            'storage/framework/sessions' => is_writable(storage_path('framework/sessions')),
            'storage/framework/views' => is_writable(storage_path('framework/views')),
            'storage/logs' => is_writable(storage_path('logs')),
        ];
    }

    /**
     * Determine whether all check values are true.
     *
     * @param array<string, bool> $checks
     */
    private function allChecksPassed(array $checks): bool
    {
        foreach ($checks as $passed) {
            if (! $passed) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate submitted database credentials.
     *
     * @param array{host: string, port: int, database: string, username: string, password: string} $credentials
     */
    private function databaseCredentialsAreValid(array $credentials): bool
    {
        $previousDefaultConnection = Config::get('database.default');
        $previousMysqlConnection = Config::get('database.connections.mysql');

        try {
            $this->setDatabaseCredentials($credentials);
            DB::connection('mysql')->getPdo();
        } catch (Throwable $exception) {
            Log::info($exception->getMessage());

            return false;
        } finally {
            Config::set('database.default', $previousDefaultConnection);
            Config::set('database.connections.mysql', $previousMysqlConnection);
            DB::purge('mysql');
        }

        return true;
    }

    /**
     * Apply submitted database credentials to the runtime configuration.
     *
     * @param array{host: string, port: int, database: string, username: string, password: string} $credentials
     */
    private function setDatabaseCredentials(array $credentials): void
    {
        Config::set('database.default', 'mysql');
        Config::set('database.connections.mysql.host', $credentials['host']);
        Config::set('database.connections.mysql.port', (string) $credentials['port']);
        Config::set('database.connections.mysql.database', $credentials['database']);
        Config::set('database.connections.mysql.username', $credentials['username']);
        Config::set('database.connections.mysql.password', $credentials['password']);

        DB::purge('mysql');
        DB::reconnect('mysql');
    }

    /**
     * Return the locale selected during installation.
     */
    private function installLocale(): string
    {
        $locale = Session::get('install.locale', app()->getLocale());

        return is_string($locale) && $this->isSupportedLocale($locale)
            ? $locale
            : Config::get('app.fallback_locale', 'en');
    }

    /**
     * Determine whether a locale is supported by the application.
     */
    private function isSupportedLocale(string $locale): bool
    {
        return array_key_exists($locale, Config::get('app.locales', []));
    }

    /**
     * Write the .env file with submitted installation values.
     *
     * @param array{host: string, port: int, database: string, username: string, password: string} $credentials
     */
    private function writeEnvironment(array $credentials, string $locale): void
    {
        $contents = $this->environmentTemplate();

        $values = [
            'APP_NAME' => 'ShortLink Pro',
            'APP_ENV' => Config::get('app.env', 'production'),
            'APP_DEBUG' => Config::get('app.debug') ? 'true' : 'false',
            'APP_URL' => url('/'),
            'APP_LOCALE' => $locale,
            'APP_FALLBACK_LOCALE' => Config::get('app.fallback_locale', 'en'),
            'APP_INSTALLED' => 'true',
            'LOG_CHANNEL' => 'stack',
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $credentials['host'],
            'DB_PORT' => (string) $credentials['port'],
            'DB_DATABASE' => $credentials['database'],
            'DB_USERNAME' => $credentials['username'],
            'DB_PASSWORD' => $credentials['password'],
            'BROADCAST_DRIVER' => env('BROADCAST_DRIVER', 'log'),
            'CACHE_DRIVER' => env('CACHE_DRIVER', 'file'),
            'QUEUE_CONNECTION' => env('QUEUE_CONNECTION', 'sync'),
            'SESSION_DRIVER' => env('SESSION_DRIVER', 'file'),
            'SESSION_LIFETIME' => (string) env('SESSION_LIFETIME', 120),
            'MAIL_MAILER' => env('MAIL_MAILER', 'log'),
            'MAIL_HOST' => env('MAIL_HOST', 'mailpit'),
            'MAIL_PORT' => (string) env('MAIL_PORT', 1025),
            'MAIL_USERNAME' => (string) env('MAIL_USERNAME', ''),
            'MAIL_PASSWORD' => (string) env('MAIL_PASSWORD', ''),
            'MAIL_ENCRYPTION' => (string) env('MAIL_ENCRYPTION', ''),
            'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
            'MAIL_FROM_NAME' => '${APP_NAME}',
            'STRIPE_KEY' => env('STRIPE_KEY', ''),
            'STRIPE_SECRET' => env('STRIPE_SECRET', ''),
            'STRIPE_WEBHOOK_SECRET' => env('STRIPE_WEBHOOK_SECRET', ''),
        ];

        foreach ($values as $key => $value) {
            $contents = $this->setEnvironmentValue($contents, $key, (string) $value);
        }

        file_put_contents(base_path('.env'), $contents);
    }

    /**
     * Return the environment file template used by the installer.
     */
    private function environmentTemplate(): string
    {
        if (file_exists(base_path('.env'))) {
            return (string) file_get_contents(base_path('.env'));
        }

        if (file_exists(base_path('.env.example'))) {
            return (string) file_get_contents(base_path('.env.example'));
        }

        return '';
    }

    /**
     * Set or append one key inside .env contents.
     */
    private function setEnvironmentValue(string $contents, string $key, string $value): string
    {
        $line = $key.'='.$this->formatEnvironmentValue($value);

        if (preg_match('/^'.preg_quote($key, '/').'=.*/m', $contents)) {
            return preg_replace('/^'.preg_quote($key, '/').'=.*/m', $line, $contents) ?? $contents;
        }

        return rtrim($contents).PHP_EOL.$line.PHP_EOL;
    }

    /**
     * Format a value so it is safe to write into .env.
     */
    private function formatEnvironmentValue(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if ($value === '${APP_NAME}') {
            return '"${APP_NAME}"';
        }

        if (preg_match('/[\s#"\'=]/', $value) === 1) {
            return '"'.str_replace('"', '\"', $value).'"';
        }

        return $value;
    }

    /**
     * Read the current .env file contents for rollback.
     */
    private function readEnvironmentFile(): ?string
    {
        return file_exists(base_path('.env'))
            ? (string) file_get_contents(base_path('.env'))
            : null;
    }

    /**
     * Reload environment variables after writing .env.
     */
    private function reloadEnvironment(): void
    {
        (new LoadEnvironmentVariables)->bootstrap(app());
    }

    /**
     * Restore the previous environment file after failed installation.
     */
    private function restoreEnvironment(?string $previousEnvironment): void
    {
        if ($previousEnvironment === null) {
            @unlink(base_path('.env'));

            return;
        }

        file_put_contents(base_path('.env'), $previousEnvironment);
        $this->reloadEnvironment();
    }

    /**
     * Create or update the first administrator account.
     */
    private function createAdministrator(AdminRequest $request): void
    {
        $user = User::withTrashed()->firstOrNew([
            'email' => (string) $request->input('email'),
        ]);

        $user->forceFill([
            'name' => (string) $request->input('name'),
            'email' => (string) $request->input('email'),
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make((string) $request->input('password')),
            'role' => 1,
            'api_token' => $user->api_token ?: Str::random(60),
            'locale' => $this->installLocale(),
            'timezone' => Config::get('app.timezone', 'UTC'),
            'deleted_at' => null,
        ])->save();

        if (method_exists($user, 'restore') && $user->trashed()) {
            $user->restore();
        }
    }

    /**
     * Mark the application as installed.
     */
    private function markInstalled(): void
    {
        file_put_contents(storage_path('installed'), 'Installed at '.Carbon::now()->toDateTimeString().PHP_EOL);
    }
}
