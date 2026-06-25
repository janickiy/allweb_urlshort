<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LocaleService
{
    /**
     * Inject dependencies used by locale operations.
     */
    public function __construct(private readonly UserRepository $users)
    {
    }

    /**
     * Persist the selected locale for the authenticated user.
     */
    public function select(?string $locale): bool
    {
        if (!is_string($locale) || !array_key_exists($locale, config('app.locales'))) {
            return false;
        }

        Cookie::queue(Cookie::make('locale', $locale, 60 * 24 * 365 * 10));

        if (Auth::check()) {
            $this->users->updateLocale(Auth::user(), $locale);
        }

        return true;
    }
}
