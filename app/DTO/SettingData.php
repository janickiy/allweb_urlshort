<?php

namespace App\DTO;

final readonly class SettingData implements DataTransferObject
{
    public function __construct(
        public ?string $name = null,
        public mixed $value = null,
        private array $fields = [],
    ) {
    }

    /**
     * Create a setting DTO from input data.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: self::nullableString($data['name'] ?? null),
            value: $data['value'] ?? null,
            fields: self::presentFields($data),
        );
    }

    /**
     * Return setting attributes for repository persistence.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->onlyPresent([
            'name' => $this->name,
            'value' => $this->value,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<int, string>
     */
    private static function presentFields(array $data): array
    {
        return array_values(array_intersect(['name', 'value'], array_keys($data)));
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
