<?php

namespace App\DTO;

final readonly class StatData implements DataTransferObject
{
    public function __construct(
        public ?int $linkId = null,
        public ?int $userId = null,
        public ?string $referrer = null,
        public ?string $platform = null,
        public ?string $browser = null,
        public ?string $device = null,
        public ?string $country = null,
        public ?string $language = null,
        private array $fields = [],
    ) {
    }

    /**
     * Create a stat DTO from collected request data.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            linkId: array_key_exists('link_id', $data) ? (int) $data['link_id'] : null,
            userId: array_key_exists('user_id', $data) ? (int) $data['user_id'] : null,
            referrer: self::nullableString($data['referrer'] ?? null),
            platform: self::nullableString($data['platform'] ?? null),
            browser: self::nullableString($data['browser'] ?? null),
            device: self::nullableString($data['device'] ?? null),
            country: self::nullableString($data['country'] ?? null),
            language: self::nullableString($data['language'] ?? null),
            fields: self::presentFields($data),
        );
    }

    /**
     * Return stat attributes for repository persistence.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->onlyPresent([
            'link_id' => $this->linkId,
            'user_id' => $this->userId,
            'referrer' => $this->referrer,
            'platform' => $this->platform,
            'browser' => $this->browser,
            'device' => $this->device,
            'country' => $this->country,
            'language' => $this->language,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<int, string>
     */
    private static function presentFields(array $data): array
    {
        return array_values(array_intersect([
            'link_id',
            'user_id',
            'referrer',
            'platform',
            'browser',
            'device',
            'country',
            'language',
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
