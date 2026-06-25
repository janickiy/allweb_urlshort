<?php

namespace App\Repositories;

use App\DTO\DataTransferObject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    /**
     * Return all records for the repository model.
     */
    public function all(): Collection;

    /**
     * Find a model by primary key or return null.
     */
    public function find(int|string $id): ?Model;

    /**
     * Find a model by primary key or throw when it does not exist.
     */
    public function findOrFail(int|string $id): Model;

    /**
     * Persist a new model instance from a data transfer object.
     */
    public function createFromDto(DataTransferObject $dto): Model;

    /**
     * Update a model by primary key with data from a DTO.
     */
    public function updateFromDto(int|string $id, DataTransferObject $dto): bool;

    /**
     * Delete a model by primary key when it exists.
     */
    public function delete(int|string $id): bool;

    /**
     * Truncate the repository model table.
     */
    public function truncate(): void;
}
