<?php

namespace App\Repositories;

use App\DTO\DataTransferObject;
use App\DTO\LanguageData;
use App\Models\Language;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class LanguageRepository extends BaseRepository
{
    /**
     * Inject the language model used by the repository.
     */
    public function __construct(Language $model)
    {
        parent::__construct($model);
    }

    /**
     * Create a new language query builder.
     */
    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Paginate languages for the admin panel with search and sorting.
     *
     * @param string|null $search
     * @param string $sort
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginateForAdmin(?string $search, string $sort = 'desc', int $perPage = 10): LengthAwarePaginator
    {
        return $this->query()
            ->when($search, fn (Builder $query) => $query->search($search))
            ->orderBy('id', $sort === 'asc' ? 'asc' : 'desc')
            ->paginate($perPage)
            ->appends(['search' => $search, 'sort' => $sort]);
    }

    /**
     * Find a language by code or throw when it does not exist.
     *
     * @param string $code
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findByCodeOrFail(string $code)
    {
        return $this->query()
            ->where('code', $code)
            ->firstOrFail();
    }

    /**
     * Return every configured language.
     */
    public function all(): Collection
    {
        return $this->query()->get();
    }

    /**
     * Count all configured languages.
     */
    public function count(): int
    {
        return $this->query()->count();
    }

    /**
     * Update an existing language by code or create a new one.
     *
     * @param string $code
     * @param DataTransferObject $dto
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateOrCreateByCode(string $code, DataTransferObject $dto)
    {
        return $this->query()->updateOrCreate(['code' => $code], $dto->toArray());
    }


    /**
     * Mark the selected language as the default language.
     *
     * @param int|string $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function makeDefault(int|string $id)
    {
        $language = $this->findOrFail($id);

        $this->query()->update(LanguageData::fromArray(['default' => 0])->toArray());
        $this->updateFromDto($language->getKey(), LanguageData::fromArray(['default' => 1]));

        return $this->findOrFail($id);
    }
}
