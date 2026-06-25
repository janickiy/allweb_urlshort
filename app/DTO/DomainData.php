<?php

namespace App\DTO;

final readonly class DomainData implements DataTransferObject
{
    public function __construct(
        public ?string $name = null,
        public ?string $indexPage = null,
        public ?string $notFoundPage = null,
        public ?int $userId = null,
        private array $fields = [],
    ) {
    }

    /**
     * Create a domain DTO from validated input.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: self::nullableString($data['name'] ?? null),
            indexPage: self::nullableString($data['index_page'] ?? null),
            notFoundPage: self::nullableString($data['not_found_page'] ?? null),
            userId: array_key_exists('user_id', $data) ? (int) $data['user_id'] : null,
            fields: self::presentFields($data),
        );
    }

    /**
     * Return domain attributes for repository persistence.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->onlyPresent([
            'name' => $this->name,
            'index_page' => $this->indexPage,
            'not_found_page' => $this->notFoundPage,
            'user_id' => $this->userId,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<int, string>
     */
    private static function presentFields(array $data): array
    {
        return array_values(array_intersect([
            'name',
            'index_page',
            'not_found_page',
            'user_id',
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
