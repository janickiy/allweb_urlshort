<?php

namespace App\DTO;

final readonly class WorkspaceData implements DataTransferObject
{
    public function __construct(
        public ?int $userId = null,
        public ?string $name = null,
        public ?int $color = null,
        private array $fields = [],
    ) {
    }

    /**
     * Create a workspace DTO from validated input.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            userId: array_key_exists('user_id', $data) ? (int) $data['user_id'] : null,
            name: self::nullableString($data['name'] ?? null),
            color: array_key_exists('color', $data) ? (int) $data['color'] : null,
            fields: self::presentFields($data),
        );
    }

    /**
     * Return workspace attributes for repository persistence.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->onlyPresent([
            'user_id' => $this->userId,
            'name' => $this->name,
            'color' => $this->color,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<int, string>
     */
    private static function presentFields(array $data): array
    {
        return array_values(array_intersect(['user_id', 'name', 'color'], array_keys($data)));
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
