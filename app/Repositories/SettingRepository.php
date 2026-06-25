<?php

namespace App\Repositories;

use App\Models\Setting;

class SettingRepository extends BaseRepository
{
    public function __construct(Setting $model)
    {
        parent::__construct($model);
    }

    public function updateByName(string $name, mixed $value): bool
    {
        return (bool) $this->model->newQuery()
            ->where('name', $name)
            ->update(['value' => $value]);
    }

    /**
     * @param array<string, mixed> $settings
     */
    public function updateManyByName(array $settings): void
    {
        foreach ($settings as $name => $value) {
            $this->updateByName($name, $value);
        }
    }
}
