<?php

namespace App\Repositories;

use App\Models\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class PageRepository extends BaseRepository
{
    public function __construct(Page $model)
    {
        parent::__construct($model);
    }


    public function findBySlugOrFail(string $slug): Page
    {
        return $this->query()
            ->where('slug', $slug)
            ->firstOrFail();
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
}
