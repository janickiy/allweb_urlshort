<?php

namespace App\Services;

use App\DTO\SettingData;
use App\Repositories\SettingRepository;
use Illuminate\Http\UploadedFile;

class SettingsService
{
    /**
     * Inject dependencies used by settings operations.
     */
    public function __construct(private readonly SettingRepository $settings)
    {
    }

    /**
     * Persist selected setting keys from the provided input.
     *
     * @param array<int, string> $keys
     * @param array<string, mixed> $input
     */
    public function updateKeys(array $keys, array $input): void
    {
        foreach ($keys as $key) {
            $this->settings->updateByName($key, SettingData::fromArray(['value' => $input[$key] ?? null]));
        }
    }

    /**
     * Persist general site settings.
     *
     * @param array<string, mixed> $input
     */
    public function updateGeneral(array $input): void
    {
        $this->updateKeys(['title', 'tagline', 'index', 'timezone', 'tracking_code'], $input);
    }

    /**
     * Persist registration settings.
     *
     * @param array<string, mixed> $input
     */
    public function updateRegistration(array $input): void
    {
        $this->updateKeys(['registration_registration', 'registration_captcha', 'registration_verification'], $input);
    }

    /**
     * Persist public contact settings.
     *
     * @param array<string, mixed> $input
     */
    public function updateContact(array $input): void
    {
        $this->updateKeys(['contact_captcha', 'contact_email'], $input);
    }

    /**
     * Persist captcha provider settings.
     *
     * @param array<string, mixed> $input
     */
    public function updateCaptcha(array $input): void
    {
        $this->updateKeys(['captcha_site_key', 'captcha_secret_key', 'captcha_registration', 'captcha_contact', 'captcha_shorten'], $input);
    }

    /**
     * Persist shortener behavior settings.
     *
     * @param array<string, mixed> $input
     */
    public function updateShortener(array $input): void
    {
        $this->updateKeys(['short_guest', 'short_bad_words'], $input);
    }

    /**
     * Persist legal link settings.
     *
     * @param array<string, mixed> $input
     */
    public function updateLegal(array $input): void
    {
        $this->updateKeys(['legal_terms_url', 'legal_privacy_url', 'legal_cookie_url'], $input);
    }

    /**
     * Persist appearance settings and replace uploaded brand assets.
     *
     * @param array<string, mixed> $input
     */
    public function updateAppearance(array $input, ?UploadedFile $logo = null, ?UploadedFile $favicon = null): void
    {
        foreach (['logo' => $logo, 'favicon' => $favicon] as $key => $file) {
            if ($file instanceof UploadedFile) {
                $this->updateKeys([$key], [$key => $this->replaceBrandAsset($key, $file)]);
            }
        }

        $this->updateKeys(['theme'], $input);
    }

    /**
     * Persist outgoing email settings.
     *
     * @param array<string, mixed> $input
     */
    public function updateEmail(array $input): void
    {
        $this->updateKeys(['email_driver', 'email_host', 'email_port', 'email_encryption', 'email_address', 'email_username', 'email_password'], $input);
    }

    /**
     * Persist social profile links.
     *
     * @param array<string, mixed> $input
     */
    public function updateSocial(array $input): void
    {
        $this->updateKeys(['social_facebook', 'social_twitter', 'social_instagram', 'social_youtube'], $input);
    }

    /**
     * Persist Stripe payment settings.
     *
     * @param array<string, mixed> $input
     */
    public function updatePayment(array $input): void
    {
        $this->updateKeys(['stripe', 'stripe_key', 'stripe_secret', 'stripe_wh_secret'], $input);
    }

    /**
     * Persist invoice profile settings.
     *
     * @param array<string, mixed> $input
     */
    public function updateInvoice(array $input): void
    {
        $this->updateKeys(['invoice_vendor', 'invoice_address', 'invoice_city', 'invoice_state', 'invoice_postal_code', 'invoice_country', 'invoice_phone', 'invoice_vat_number'], $input);
    }

    /**
     * Replace one brand asset on disk and return the stored file name.
     */
    private function replaceBrandAsset(string $key, UploadedFile $file): string
    {
        $fileName = $file->hashName();
        $currentFile = config('settings.' . $key);

        if (is_string($currentFile) && $currentFile !== '') {
            $currentPath = public_path('uploads/brand/' . $currentFile);

            if (file_exists($currentPath)) {
                unlink($currentPath);
            }
        }

        $file->move(public_path('uploads/brand'), $fileName);

        return $fileName;
    }
}
