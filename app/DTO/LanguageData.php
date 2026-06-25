<?php

namespace App\DTO;

final readonly class LanguageData implements DataTransferObject
{
    public function __construct(
        public ?string $code = null,
        public ?string $name = null,
        public ?string $dir = null,
        public ?int $default = null,
        private array $fields = [],
    ) {
    }

    /**
     * Create a language DTO from input data.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            code: self::nullableString($data['code'] ?? null),
            name: self::nullableString($data['name'] ?? null),
            dir: self::nullableString($data['dir'] ?? null),
            default: array_key_exists('default', $data) ? (int) $data['default'] : null,
            fields: self::presentFields($data),
        );
    }

    /**
     * Return language attributes for repository persistence.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->onlyPresent([
            'code' => $this->code,
            'name' => $this->name,
            'dir' => $this->dir,
            'default' => $this->default,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<int, string>
     */
    private static function presentFields(array $data): array
    {
        return array_values(array_intersect(['code', 'name', 'dir', 'default'], array_keys($data)));
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
