<?php

namespace App\Repositories;

use App\Models\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class PageRepository extends BaseRepository
{
    /**
     * Inject the page model used by the repository.
     */
    public function __construct(Page $model)
    {
        parent::__construct($model);
    }


    /**
     * Find a page by slug or throw when it does not exist.
     */
    public function findBySlugOrFail(string $slug)
    {
        return $this->query()
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * Create a new page query builder.
     */
    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Paginate pages for the admin panel with search and sorting.
     */
    public function paginateForAdmin(?string $search, string $sort = 'desc', int $perPage = 10): LengthAwarePaginator
    {
        return $this->query()
            ->when($search, fn (Builder $query) => $query->search($search))
            ->orderBy('id', $sort === 'asc' ? 'asc' : 'desc')
            ->paginate($perPage)
            ->appends(['search' => $search, 'sort' => $sort]);
    }
}
