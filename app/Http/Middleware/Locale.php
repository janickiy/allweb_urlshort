<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class Locale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Get all the available languages
            $configuredLanguages = config('app.locales');
            $languages = is_array($configuredLanguages) ? $configuredLanguages : [];

            if ($request->is('install*')) {
                $this->setInstallerLocale($request, $languages);

                return $next($request);
            }

            if (Auth::check()) {
                $userLocale = Auth::user()->locale;

                if (array_key_exists($userLocale, $languages)) {
                    App::setLocale($userLocale);
                }
            } else if (Cookie::has('locale')) {
                // Get the current language
                $language = Cookie::get('locale');

                if (array_key_exists($language, $languages)) {
                    App::setLocale($language);
                }
            } else {
                App::setLocale(config('app.locale'));
            }
        } catch (\Exception $e) {
        }

        return $next($request);
    }

    /**
     * Set installer locale from explicit selection, browser language, or English fallback.
     */
    private function setInstallerLocale(Request $request, array $languages): void
    {
        $sessionLocale = $this->sessionLocale($request, $languages);
        $locale = $sessionLocale
            ?? $this->installerCookieLocale($languages)
            ?? $this->browserLocale($request, $languages)
            ?? $this->fallbackLocale($languages);

        App::setLocale($locale);

        if ($request->hasSession() && $sessionLocale === null) {
            $request->session()->put('install.locale', $locale);
        }
    }

    /**
     * Return the installer locale selected in the current session.
     */
    private function sessionLocale(Request $request, array $languages): ?string
    {
        if (! $request->hasSession()) {
            return null;
        }

        $locale = $request->session()->get('install.locale');

        return is_string($locale) && array_key_exists($locale, $languages) ? $locale : null;
    }

    /**
     * Return the installer locale selected manually in a previous browser session.
     */
    private function installerCookieLocale(array $languages): ?string
    {
        $locale = Cookie::get('install_locale');

        return is_string($locale) && array_key_exists($locale, $languages) ? $locale : null;
    }

    /**
     * Detect the best supported installer locale from the browser Accept-Language header.
     */
    private function browserLocale(Request $request, array $languages): ?string
    {
        foreach ($request->getLanguages() as $language) {
            $normalized = strtolower(str_replace('_', '-', $language));
            $primary = strtok($normalized, '-');

            if (array_key_exists($normalized, $languages)) {
                return $normalized;
            }

            if (is_string($primary) && array_key_exists($primary, $languages)) {
                return $primary;
            }

            if (($primary === 'zh' || $primary === 'cn') && array_key_exists('cn', $languages)) {
                return 'cn';
            }
        }

        return null;
    }

    /**
     * Return the default installer locale.
     */
    private function fallbackLocale(array $languages): string
    {
        if (array_key_exists('en', $languages)) {
            return 'en';
        }

        $fallbackLocale = config('app.fallback_locale', 'en');

        return is_string($fallbackLocale) && array_key_exists($fallbackLocale, $languages)
            ? $fallbackLocale
            : 'en';
    }
}
