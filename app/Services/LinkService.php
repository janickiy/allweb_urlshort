<?php

namespace App\Services;

use App\DTO\LinkData;
use App\Models\Link;
use App\Models\User;
use App\Repositories\LinkRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class LinkService
{
    public function __construct(
        private readonly AliasGenerator $aliases,
        private readonly LinkRepository $links,
        private readonly UrlMetadataService $metadata,
    ) {
    }

    public function create(array $input, ?User $user = null): Link
    {
        return $this->links->createFromDto(LinkData::fromArray(
            $this->attributesForCreate($input, $user?->id ?? 0)
        ));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function createMany(array $input, User $user): array
    {
        $urls = preg_split('/\n|\r/', $input['urls'] ?? '', -1, PREG_SPLIT_NO_EMPTY);
        $now = Carbon::now();
        $rows = [];

        foreach ($urls as $url) {
            $metadata = $this->metadata->parse($url);

            $rows[] = [
                'user_id' => $user->id,
                'url' => $url,
                'alias' => $this->aliases->generate($input['domain'] ?? null),
                'title' => isset($metadata['title']) ? trim($metadata['title']) : null,
                'space_id' => $input['space'] ?? null,
                'domain_id' => $input['domain'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $this->links->bulkInsert($rows);

        return $rows;
    }

    public function update(Link $link, array $input): Link
    {
        $this->links->updateFromDto($link->id, LinkData::fromArray(
            $this->attributesForUpdate($input, $link)
        ));

        return $link->refresh();
    }

    public function delete(Link $link): bool
    {
        return (bool) $link->delete();
    }

    public function latestForUser(int $userId, int $limit): Collection
    {
        return $this->links->latestForUser($userId, $limit);
    }

    public function displayName(Link $link): string
    {
        return str_replace(
            ['http://', 'https://'],
            '',
            isset($link->domain) ? $link->domain->name.'/'.$link->alias : route('link.redirect', $link->alias)
        );
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
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
     * @param array<string, mixed> $input
     * @return array<string, mixed>
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
     * @param mixed $rules
     * @return array<int, array<string, mixed>>|null
     */
    private function targetRules(mixed $rules): ?array
    {
        if (!is_array($rules)) {
            return null;
        }

        $rules = array_values(array_filter(array_map('array_filter', array_values($rules))));

        return $rules === [] ? null : $rules;
    }
}
