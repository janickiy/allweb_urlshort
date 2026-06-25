<?php

namespace App\Repositories;

use App\Models\Language;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class LanguageRepository extends BaseRepository
{
    public function __construct(Language $model)
    {
        parent::__construct($model);
    }

    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    public function paginateForAdmin(?string $search, string $sort = 'desc', int $perPage = 10): LengthAwarePaginator
    {
        return $this->query()
            ->when($search, fn (Builder $query) => $query->search($search))
            ->orderBy('id', $sort === 'asc' ? 'asc' : 'desc')
            ->paginate($perPage)
            ->appends(['search' => $search, 'sort' => $sort]);
    }

    public function findByCodeOrFail(string $code): Language
    {
        return $this->query()
            ->where('code', $code)
            ->firstOrFail();
    }

    public function all(): Collection
    {
        return $this->query()->get();
    }

    public function count(): int
    {
        return $this->query()->count();
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function updateOrCreateByCode(string $code, array $attributes): Language
    {
        return $this->query()->updateOrCreate(['code' => $code], $attributes);
    }

    public function makeDefault(int|string $id): Language
    {
        $language = $this->findOrFail($id);

        $this->query()->update(['default' => 0]);
        $language->forceFill(['default' => 1])->save();

        return $language;
    }
}
