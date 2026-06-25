<?php

namespace App\Services;

use App\DTO\LinkData;
use App\Models\Link;
use App\Models\User;
use App\Repositories\LinkRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class LinkService
{
    /**
     * Inject dependencies used by link operations.
     */
    public function __construct(
        private readonly AliasGenerator $aliases,
        private readonly LinkRepository $links,
        private readonly UrlMetadataService $metadata,
    ) {
    }

    /**
     * Create a single short link from input data.
     */
    public function create(array $input, ?User $user = null)
    {
        return $this->links->createFromDto(LinkData::fromArray(
            $this->attributesForCreate($input, $user?->id ?? 0)
        ));
    }

    /**
     * Create links for a user and return the newly created links for UI feedback.
     *
     * @param array<string, mixed> $input
     */
    public function createForUser(array $input, User $user): Collection
    {
        if (!empty($input['multi_link'])) {
            $created = $this->createMany($input, $user);

            return $this->latestForUser($user->id, count($created));
        }

        $this->create($input, $user);

        return $this->latestForUser($user->id, 1);
    }

    /**
     * Create a public guest link and return it for UI feedback.
     *
     * @param array<string, mixed> $input
     */
    public function createForGuest(array $input): Collection
    {
        if (!config('settings.short_guest')) {
            abort(404);
        }

        $this->create($input);

        return $this->latestForUser(0, 1);
    }

    /**
     * Create multiple short links from a newline-separated URL list.
     *
     * @param array $input
     * @param User $user
     * @return array
     */
    public function createMany(array $input, User $user): array
    {
        $urls = preg_split('/\n|\r/', $input['urls'] ?? '', -1, PREG_SPLIT_NO_EMPTY);
        $dtos = [];
        $rows = [];

        foreach ($urls as $url) {
            $metadata = $this->metadata->parse($url);

            $dto = LinkData::fromArray([
                'user_id' => $user->id,
                'url' => $url,
                'alias' => $this->aliases->generate($input['domain'] ?? null),
                'title' => isset($metadata['title']) ? trim($metadata['title']) : null,
                'space_id' => $input['space'] ?? null,
                'domain_id' => $input['domain'] ?? null,
            ]);

            $dtos[] = $dto;
            $rows[] = $dto->toArray();
        }

        $this->links->bulkInsertFromDtos($dtos);

        return $rows;
    }

    /**
     * Update an existing short link from input data.
     *
     * @param Link $link
     * @param array $input
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(Link $link, array $input)
    {
        $this->links->updateFromDto($link->id, LinkData::fromArray(
            $this->attributesForUpdate($input, $link)
        ));

        return $this->links->findOrFail($link->id);
    }

    /**
     * Update a link owned by a user.
     *
     * @param int|string $id
     * @param User $user
     * @param array $input
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateForUser(int|string $id, User $user, array $input)
    {
        return $this->update($this->links->findForUserOrFail($id, $user->id), $input);
    }

    /**
     * Return a link owned by a user or null.
     */
    public function findForUser(int|string $id, User $user): ?Link
    {
        return $this->links->findForUser($id, $user->id);
    }

    /**
     * Update a link by primary key for admin workflows.
     *
     * @param array<string, mixed> $input
     */
    public function updateById(int|string $id, array $input)
    {
        return $this->update($this->links->findOrFail($id), $input);
    }

    /**
     * Delete a short link.
     */
    public function delete(Link $link): bool
    {
        return $this->links->delete($link->id);
    }

    /**
     * Delete a link owned by a user and return its display name.
     */
    public function deleteForUser(int|string $id, User $user): string
    {
        return $this->deleteAndReturnName($this->links->findForUserOrFail($id, $user->id));
    }

    /**
     * Delete a link owned by a user and return the deleted link.
     */
    public function deleteForApiUser(int|string $id, User $user): ?Link
    {
        $link = $this->findForUser($id, $user);

        if (!$link) {
            return null;
        }

        $this->delete($link);

        return $link;
    }

    /**
     * Delete a link by primary key for admin workflows and return its display name.
     */
    public function deleteById(int|string $id): string
    {
        return $this->deleteAndReturnName($this->links->findOrFail($id));
    }

    /**
     * Return the latest links for a user.
     *
     * @param int $userId
     * @param int $limit
     * @return Collection
     */
    public function latestForUser(int $userId, int $limit): Collection
    {
        return $this->links->latestForUser($userId, $limit);
    }

    /**
     * Paginate the latest links for a user.
     *
     * @param int $userId
     * @return LengthAwarePaginator
     */
    public function paginateLatestForUser(int $userId): LengthAwarePaginator
    {
        return $this->links->paginateLatestForUser($userId);
    }

    /**
     * Build the display name shown for a short link.
     */
    public function displayName(Link $link): string
    {
        $domainName = $this->links->domainName($link);

        return str_replace(
            ['http://', 'https://'],
            '',
            $domainName ? $domainName.'/'.$link->alias : route('link.redirect', $link->alias)
        );
    }

    /**
     * Delete a link model and return the name that should be shown to users.
     */
    private function deleteAndReturnName(Link $link): string
    {
        $name = $this->displayName($link);

        $this->delete($link);

        return $name;
    }

    /**
     * Map create input into link repository attribute
     *
     * @param array $input
     * @param int $userId
     * @return array
     */
    private function attributesForCreate(array $input, int $userId): array
    {
        $attributes = [
            'user_id' => $userId,
            'alias' => $input['alias'] ?? $this->aliases->generate($input['domain'] ?? null),
        ];

        return array_merge($attributes, $this->sharedAttributes($input));
    }

    /**
     * Map update input into link repository attributes.
     *
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    private function attributesForUpdate(array $input, Link $link): array
    {
        $attributes = $this->sharedAttributes($input, $link);

        if (array_key_exists('alias', $input)) {
            $attributes['alias'] = $input['alias'];
        }

        return $attributes;
    }

    /**
     * Map shared link input fields into repository attributes.
     *
     * @param array $input
     * @param Link|null $link
     * @return array
     */
    private function sharedAttributes(array $input, ?Link $link = null): array
    {
        $attributes = [];

        if (array_key_exists('url', $input)) {
            $metadata = $this->metadata->parse($input['url']);
            $attributes['url'] = $input['url'];
            $attributes['title'] = isset($metadata['title']) ? trim($metadata['title']) : null;
        }

        if (array_key_exists('disabled', $input)) {
            $attributes['disabled'] = (int) (bool) $input['disabled'];
        }

        if (array_key_exists('public', $input)) {
            $attributes['public'] = (int) (bool) $input['public'];
        }

        if (array_key_exists('space', $input)) {
            $attributes['space_id'] = $input['space'] ?: null;
        }

        if (!$link && array_key_exists('domain', $input)) {
            $attributes['domain_id'] = $input['domain'] ?: null;
        }

        if (array_key_exists('expiration_url', $input)) {
            $attributes['expiration_url'] = $input['expiration_url'] ?: null;
        }

        if (array_key_exists('expiration_date', $input) || array_key_exists('expiration_time', $input)) {
            $attributes['ends_at'] = !empty($input['expiration_date']) && !empty($input['expiration_time'])
                ? Carbon::createFromFormat('Y-m-d H:i', $input['expiration_date'].' '.$input['expiration_time'])->toDateTimeString()
                : null;
        }

        if (array_key_exists('password', $input)) {
            $password = $input['password'];
            $attributes['password'] = $link && $password === $link->password
                ? $link->password
                : (!empty($password) ? Hash::make($password) : null);
        }

        if (array_key_exists('geo', $input)) {
            $attributes['geo_target'] = $this->targetRules($input['geo']);
        }

        if (array_key_exists('platform', $input)) {
            $attributes['platform_target'] = $this->targetRules($input['platform']);
        }

        return $attributes;
    }

    /**
     * Normalize geo or platform targeting rules.
     *
     * @return array<int, array<string, mixed>>|null
     */
    private function targetRules(?array $rules): ?array
    {
        if (!is_array($rules)) {
            return null;
        }

        $rules = array_values(array_filter(array_map('array_filter', array_values($rules))));

        return $rules === [] ? null : $rules;
    }
}
