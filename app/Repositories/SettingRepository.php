<?php

namespace App\Repositories;

use App\DTO\DataTransferObject;
use App\Models\Setting;

class SettingRepository extends BaseRepository
{
    /**
     * Inject the setting model used by the repository.
     */
    public function __construct(Setting $model)
    {
        parent::__construct($model);
    }

    /**
     * Update a setting value by setting name.
     */
    public function updateByName(string $name, DataTransferObject $dto): bool
    {
        return (bool) $this->model->newQuery()
            ->where('name', $name)
            ->update($dto->toArray());
    }

    /**
     * Update multiple setting values by setting name.
     *
     * @param array<string, DataTransferObject> $settings
     */
    public function updateManyByName(array $settings): void
    {
        foreach ($settings as $name => $dto) {
            $this->updateByName($name, $dto);
        }
    }
}
