<?php

/**
 * Format the page title segments into a single string.
 */
function formatTitle(mixed $value = null): mixed
{
    if (is_array($value)) {
        return implode(' - ', $value);
    }

    return $value;
}

/**
 * Calculate the growth between two numeric values.
 */
function calcGrowth(int|float|null $current, int|float|null $previous): int|float
{
    if ($previous == 0 || $previous == null || $current == 0) {
        return 0;
    }

    return (($current - $previous) / $previous * 100);
}

/**
 * Get and format the Gravatar URL.
 */
function gravatar(string $email, int $size = 80, string $default = 'identicon', string $rating = 'g'): string
{
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5(mb_strtolower(trim($email)));
    $url .= '?s=' . $size . '&d=' . $default . '&r=' . $rating;

    return $url;
}

/**
 * Format money using the configured currency precision.
 */
function formatMoney(int|float $amount, string $currency): string
{
    if (in_array(strtoupper($currency), config('currencies.stripe.zero-decimals'))) {
        return number_format($amount, 0, __('.'), __(','));
    }

    return number_format($amount / 100, 2, __('.'), __(','));
}

/**
 * Format the workspace color code map.
 */
function formatWorkspace(): array
{
    return [
        1 => 'success',
        2 => 'danger',
        3 => 'warning',
        4 => 'info',
        5 => 'dark',
        6 => 'primary',
    ];
}

/**
 * Format the browser icon key.
 */
function formatBrowser(mixed $key): string
{
    $browser = [
        'Chrome' => 'chrome',
        'Firefox' => 'firefox',
        'Firefox Mobile' => 'firefox',
        'Edge' => 'edge',
        'Internet Explorer' => 'ie',
        'Mobile Internet Explorer' => 'ie',
        'Vivaldi' => 'vivaldi',
        'Brave' => 'brave',
        'Safari' => 'safari',
        'Opera' => 'opera',
        'Opera Mini' => 'opera',
        'Opera Mobile' => 'opera',
        'UC Browser' => 'ucbrowser',
        'BlackBerry Browser' => 'bbbrowser',
    ];

    if (array_key_exists($key, $browser)) {
        return $browser[$key];
    }

    return 'unknown';
}

/**
 * Format the device icon key.
 */
function formatDevice(mixed $key): string
{
    $device = [
        'desktop' => 'desktop',
        'mobile' => 'mobile',
        'tablet' => 'tablet',
        'television' => 'tv',
        'gaming' => 'gaming',
        'watch' => 'watch',
    ];

    if (array_key_exists($key, $device)) {
        return $device[$key];
    }

    return 'unknown';
}

/**
 * Format the platform icon key.
 */
function formatPlatform(mixed $key): string
{
    $platform = [
        'Windows' => 'windows',
        'Linux' => 'linux',
        'Ubuntu' => 'ubuntu',
        'Windows Phone' => 'windows',
        'iOS' => 'apple',
        'OS X' => 'apple',
        'FreeBSD' => 'freebsd',
        'Android' => 'android',
        'Chrome OS' => 'chromeos',
        'BlackBerry OS' => 'bbos',
        'BlackBerry Tablet OS' => 'bbos',
    ];

    if (array_key_exists($key, $platform)) {
        return $platform[$key];
    }

    return 'unknown';
}

/**
 * Format the country icon key.
 */
function formatCountry(mixed $key): string
{
    if (array_key_exists($key, config('countries'))) {
        return strtolower($key);
    }

    return 'unknown';
}

/**
 * Format the Stripe status codes for UI badges.
 */
function formatStripeStatus(): array
{
    return [
        'emulated' => ['status' => 'dark', 'title' => __('Emulated')],
        'trialing' => ['status' => 'success', 'title' => __('Trialing')],
        'active' => ['status' => 'success', 'title' => __('Active')],
        'incomplete' => ['status' => 'warning', 'title' => __('Incomplete')],
        'incomplete_expired' => ['status' => 'danger', 'title' => __('Expired')],
        'past_due' => ['status' => 'warning', 'title' => __('Past due')],
        'canceled' => ['status' => 'danger', 'title' => __('Canceled')],
        'unpaid' => ['status' => 'danger', 'title' => __('Unpaid')],
    ];
}
