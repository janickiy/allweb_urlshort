<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

class ReCaptchaV3Service
{
    private const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Verify a reCAPTCHA v3 token against Google and enforce action and score.
     */
    public function verify(?string $token, string $action, ?string $ip = null): bool
    {
        $token = is_string($token) ? trim($token) : '';
        $secret = config('captcha.secret');

        if ($token === '' || ! is_string($secret) || trim($secret) === '') {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout((int) config('captcha.options.timeout', 5))
                ->post(self::VERIFY_URL, array_filter([
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $ip,
                ]));

            if (! $response->ok()) {
                return false;
            }

            $payload = $response->json();
        } catch (Throwable) {
            return false;
        }

        if (! is_array($payload)) {
            return false;
        }

        return ($payload['success'] ?? false) === true
            && ($payload['action'] ?? null) === $action
            && (float) ($payload['score'] ?? 0) >= (float) config('captcha.score_threshold', 0.5);
    }
}
