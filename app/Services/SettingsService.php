<?php

namespace App\Services;

use App\Repositories\SettingRepository;

class SettingsService
{
    public function __construct(private readonly SettingRepository $settings)
    {
    }

    /**
     * @param array<int, string> $keys
     * @param array<string, mixed> $input
     */
    public function updateKeys(array $keys, array $input): void
    {
        foreach ($keys as $key) {
            $this->settings->updateByName($key, $input[$key] ?? null);
        }
    }
}
