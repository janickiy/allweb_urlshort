<?php

namespace App\DTO;

final readonly class LinkData implements DataTransferObject
{
    public function __construct(
        public ?int $userId = null,
        public ?string $alias = null,
        public ?string $url = null,
        public ?string $title = null,
        public mixed $geoTarget = null,
        public mixed $platformTarget = null,
        public ?string $password = null,
        public ?int $disabled = null,
        public ?int $isPublic = null,
        public ?string $expirationUrl = null,
        public ?int $clicks = null,
        public ?int $spaceId = null,
        public ?int $domainId = null,
        public mixed $endsAt = null,
        private array $fields = [],
    ) {
    }

    /**
     * Create a link DTO from validated input.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            userId: array_key_exists('user_id', $data) ? (int) $data['user_id'] : null,
            alias: self::nullableString($data['alias'] ?? null),
            url: self::nullableString($data['url'] ?? null),
            title: self::nullableString($data['title'] ?? null),
            geoTarget: $data['geo_target'] ?? null,
            platformTarget: $data['platform_target'] ?? null,
            password: self::nullableString($data['password'] ?? null),
            disabled: array_key_exists('disabled', $data) ? (int) $data['disabled'] : null,
            isPublic: array_key_exists('public', $data) ? (int) $data['public'] : null,
            expirationUrl: self::nullableString($data['expiration_url'] ?? null),
            clicks: array_key_exists('clicks', $data) ? (int) $data['clicks'] : null,
            spaceId: array_key_exists('space_id', $data) && $data['space_id'] !== null ? (int) $data['space_id'] : null,
            domainId: array_key_exists('domain_id', $data) && $data['domain_id'] !== null ? (int) $data['domain_id'] : null,
            endsAt: $data['ends_at'] ?? null,
            fields: self::presentFields($data),
        );
    }

    /**
     * Return link attributes for repository persistence.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->onlyPresent([
            'user_id' => $this->userId,
            'alias' => $this->alias,
            'url' => $this->url,
            'title' => $this->title,
            'geo_target' => $this->geoTarget,
            'platform_target' => $this->platformTarget,
            'password' => $this->password,
            'disabled' => $this->disabled,
            'public' => $this->isPublic,
            'expiration_url' => $this->expirationUrl,
            'clicks' => $this->clicks,
            'space_id' => $this->spaceId,
            'domain_id' => $this->domainId,
            'ends_at' => $this->endsAt,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<int, string>
     */
    private static function presentFields(array $data): array
    {
        return array_values(array_intersect([
            'user_id',
            'alias',
            'url',
            'title',
            'geo_target',
            'platform_target',
            'password',
            'disabled',
            'public',
            'expiration_url',
            'clicks',
            'space_id',
            'domain_id',
            'ends_at',
        ], array_keys($data)));
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function onlyPresent(array $payload): array
    {
        return array_intersect_key($payload, array_flip($this->fields));
    }

    private static function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }
}
