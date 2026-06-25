<?php

namespace App\Repositories;


use App\DTO\DataTransferObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements RepositoryInterface
{

    /**
     * Inject the Eloquent model used by the base repository.
     *
     * @param Model $model
     */
    public function __construct(protected Model $model)
    {
    }

    /**
     * Create a new Eloquent query builder for the repository model.
     */
    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Persist a new model instance from a data transfer object.
     */
    public function createFromDto(DataTransferObject $dto): Model
    {
        return $this->create($dto->toArray());
    }

    /**
     * Update a model by primary key with data from a DTO.
     */
    public function updateFromDto(int|string $id, DataTransferObject $dto): bool
    {
        return $this->update($id, $dto->toArray());
    }

    /**
     * Return all records for the repository model.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Find a model by primary key or return null.
     *
     * @return Model|null
     */
    public function find(int|string $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Find a model by primary key or throw when it does not exist.
     */
    public function findOrFail(int|string $id): Model
    {
        return $this->model->newQuery()->findOrFail($id);
    }

    /**
     * Delete a model by primary key when it exists.
     */
    public function delete(int|string $id): bool
    {
        $model = $this->model->find($id);
        if ($model) {
            $model->delete();
            return true;
        }
        return false;
    }

    /**
     * Delete every record for the repository model.
     */
    public function deleteAll(): void
    {
        $this->model->query()->delete();
    }

    /**
     * Truncate the repository model table.
     */
    public function truncate(): void
    {
        $this->model->truncate();
    }

    /**
     * Persist a new model instance from sanitized DTO attributes.
     *
     * @param array<string, mixed> $data
     */
    protected function create(array $data): Model
    {
        $model = $this->model->newInstance();
        $model->forceFill($data);
        $model->save();

        return $model;
    }

    /**
     * Update a model by primary key with sanitized DTO attributes.
     *
     * @param array<string, mixed> $data
     */
    protected function update(int|string $id, array $data): bool
    {
        $model = $this->model->find($id);

        if ($model) {
            return $model->forceFill($data)->save();
        }

        return false;
    }

}
