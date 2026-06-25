<?php

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EnvironmentController extends Controller
{
    /**
     * Inject dependencies used to write installer environment files.
     */
    public function __construct(private readonly Filesystem $files)
    {
    }

    /**
     * Validate installer environment input and persist the generated environment file.
     */
    public function saveWizard(Request $request, Redirector $redirect): RedirectResponse
    {
        $rules = config('installer.environment.form.rules', []);
        $messages = [
            'environment_custom.required_if' => trans('installer_messages.environment.wizard.form.name_required'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return $redirect->route('LaravelInstaller::environmentWizard')->withInput()->withErrors($validator->errors());
        }

        if (! $this->checkDatabaseConnection($request)) {
            return $redirect->route('LaravelInstaller::environmentWizard')->withInput()->withErrors([
                'database_connection' => trans('installer_messages.environment.wizard.form.db_connection_failed'),
            ]);
        }

        $results = $this->saveEnvironmentFile($request);

        return $redirect->route('LaravelInstaller::database', [$request])
            ->with(['results' => $results]);
    }

    /**
     * Verify installer database credentials before continuing installation.
     */
    private function checkDatabaseConnection(Request $request): bool
    {
        $connection = $request->input('database_connection');
        $temporaryConnection = 'installer_check';
        $settings = config("database.connections.$connection", ['driver' => $connection]);
        $originalConnections = config('database.connections');

        config([
            "database.connections.$temporaryConnection" => array_merge($settings, [
                'driver' => $connection,
                'host' => $request->input('database_hostname'),
                'port' => $request->input('database_port'),
                'database' => $request->input('database_name'),
                'username' => $request->input('database_username'),
                'password' => $request->input('database_password'),
            ]),
        ]);

        DB::purge($temporaryConnection);

        try {
            DB::connection($temporaryConnection)->getPdo();

            return true;
        } catch (\Exception) {
            return false;
        } finally {
            DB::purge($temporaryConnection);

            config(['database.connections' => $originalConnections]);
        }
    }

    /**
     * Write the validated installer environment values to the application .env file.
     */
    private function saveEnvironmentFile(Request $request): string
    {
        $this->files->put(base_path('.env'), $this->buildEnvironmentFile($request));

        return __('Environment file saved successfully.');
    }

    /**
     * Build .env content from installer wizard input.
     */
    private function buildEnvironmentFile(Request $request): string
    {
        $environment = $request->input('environment') === 'other'
            ? $request->input('environment_custom')
            : $request->input('environment');

        $lines = [
            $this->envLine('APP_NAME', $request->input('app_name')),
            $this->envLine('APP_ENV', $environment),
            $this->envLine('APP_KEY', config('app.key')),
            $this->envLine('APP_DEBUG', $request->input('app_debug')),
            $this->envLine('APP_URL', $request->input('app_url')),
            '',
            $this->envLine('LOG_CHANNEL', 'stack'),
            $this->envLine('LOG_LEVEL', $request->input('app_log_level')),
            '',
            $this->envLine('DB_CONNECTION', $request->input('database_connection')),
            $this->envLine('DB_HOST', $request->input('database_hostname')),
            $this->envLine('DB_PORT', $request->input('database_port')),
            $this->envLine('DB_DATABASE', $request->input('database_name')),
            $this->envLine('DB_USERNAME', $request->input('database_username')),
            $this->envLine('DB_PASSWORD', $request->input('database_password')),
            '',
            $this->envLine('BROADCAST_DRIVER', $request->input('broadcast_driver')),
            $this->envLine('CACHE_DRIVER', $request->input('cache_driver')),
            $this->envLine('QUEUE_CONNECTION', $request->input('queue_driver')),
            $this->envLine('SESSION_DRIVER', $request->input('session_driver')),
            '',
            $this->envLine('REDIS_HOST', $request->input('redis_hostname')),
            $this->envLine('REDIS_PASSWORD', $request->input('redis_password')),
            $this->envLine('REDIS_PORT', $request->input('redis_port')),
            '',
            $this->envLine('MAIL_DRIVER', $request->input('mail_driver')),
            $this->envLine('MAIL_HOST', $request->input('mail_host')),
            $this->envLine('MAIL_PORT', $request->input('mail_port')),
            $this->envLine('MAIL_USERNAME', $request->input('mail_username')),
            $this->envLine('MAIL_PASSWORD', $request->input('mail_password')),
            $this->envLine('MAIL_ENCRYPTION', $request->input('mail_encryption')),
            $this->envLine('MAIL_FROM_ADDRESS', $request->input('mail_from_address')),
            $this->envLine('MAIL_FROM_NAME', $request->input('mail_from_name')),
            '',
            $this->envLine('PUSHER_APP_ID', $request->input('pusher_app_id')),
            $this->envLine('PUSHER_APP_KEY', $request->input('pusher_app_key')),
            $this->envLine('PUSHER_APP_SECRET', $request->input('pusher_app_secret')),
            $this->envLine('PUSHER_APP_CLUSTER', 'mt1'),
            '',
            $this->envLine('MIX_PUSHER_APP_KEY', '${PUSHER_APP_KEY}'),
            $this->envLine('MIX_PUSHER_APP_CLUSTER', '${PUSHER_APP_CLUSTER}'),
        ];

        return implode(PHP_EOL, $lines).PHP_EOL;
    }

    /**
     * Format one .env key-value pair.
     */
    private function envLine(string $key, mixed $value): string
    {
        return $key.'='.$this->normalizeEnvValue($value);
    }

    /**
     * Normalize a value for safe .env storage.
     */
    private function normalizeEnvValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        if (preg_match('/\s|#|"|\'/', $value)) {
            return '"'.addcslashes($value, '\\"').'"';
        }

        return $value;
    }
}
