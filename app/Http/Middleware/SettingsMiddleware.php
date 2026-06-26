<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SettingsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ((! file_exists(base_path('.env')) || ! file_exists(storage_path('installed'))) && $request->is('install*')) {
            return $next($request);
        }

        try {
            $settings = Setting::all()->pluck('value', 'name');

            // Set the app's name
            config(['app.name' => $settings['title']]);

            // Store all the database settings in a config array
            foreach ($settings as $key => $value) {
                config(['settings.' . $key => $value]);
            }

            // Set the app's default theme
            if (!Cookie::has('dark_mode')) {
                Cookie::queue(Cookie::make('dark_mode', config('settings.theme'), (60 * 24 * 365 * 10), null, null, false, false));

                config(['settings.dark_mode' => config('settings.theme')]);
            } else {
                // Rewrite the settings.theme with the user's preference
                if (Cookie::get('dark_mode') == 1) {
                    config(['settings.dark_mode' => 1]);
                } else {
                    config(['settings.dark_mode' => 0]);
                }
            }

            // If cookie law is enabled
            if (config('settings.legal_cookie_url')) {

                // Set the cookie law
                if (!Cookie::has('cookie_law')) {
                    Cookie::queue(Cookie::make('cookie_law', 0, (60 * 24 * 365 * 10), null, null, false, false));
                }
            }

            // Set the app's default mail settings.
            $mailDriver = (string) config('settings.email_driver', 'log');
            $mailHost = (string) config('settings.email_host', '127.0.0.1');
            $mailPort = (int) config('settings.email_port', 2525);
            $mailEncryption = (string) config('settings.email_encryption');
            $mailUsername = config('settings.email_username') ?: null;
            $mailPassword = config('settings.email_password') ?: null;

            config([
                'mail.default' => $mailDriver,
                'mail.driver' => $mailDriver,
                'mail.host' => $mailHost,
                'mail.port' => $mailPort,
                'mail.encryption' => $mailEncryption,
                'mail.username' => $mailUsername,
                'mail.password' => $mailPassword,
                'mail.from.address' => config('settings.email_address'),
                'mail.from.name' => config('settings.title'),
                'mail.mailers.smtp.host' => $mailHost,
                'mail.mailers.smtp.port' => $mailPort,
                'mail.mailers.smtp.scheme' => $this->mailScheme($mailEncryption),
                'mail.mailers.smtp.username' => $mailUsername,
                'mail.mailers.smtp.password' => $mailPassword,
            ]);

            if (app()->bound('mail.manager')) {
                app('mail.manager')->forgetMailers();
            }

            // Set the tripe settings
            config(['cashier.key' => config('settings.stripe_key')]);
            config(['cashier.secret' => config('settings.stripe_secret')]);
            config(['cashier.webhook.secret' => config('settings.stripe_wh_secret')]);

            // Set the reCaptcha keys
            config(['captcha.sitekey' => config('settings.captcha_site_key')]);
            config(['captcha.secret' => config('settings.captcha_secret_key')]);


        } catch (\Exception $e) {
        }

        return $next($request);
    }

    /**
     * Convert the legacy encryption setting into the Symfony mailer scheme.
     */
    private function mailScheme(string $encryption): ?string
    {
        return in_array(strtolower($encryption), ['ssl', 'smtps'], true) ? 'smtps' : null;
    }
}
