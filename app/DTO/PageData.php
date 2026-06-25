<?php

namespace App\DTO;

final readonly class PageData implements DataTransferObject
{
    public function __construct(
        public ?string $title = null,
        public ?string $slug = null,
        public ?int $footer = null,
        public ?string $content = null,
        private array $fields = [],
    ) {
    }

    /**
     * Create a page DTO from validated input.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: self::nullableString($data['title'] ?? null),
            slug: self::nullableString($data['slug'] ?? null),
            footer: array_key_exists('footer', $data) ? (int) $data['footer'] : null,
            content: array_key_exists('content', $data) ? (string) $data['content'] : null,
            fields: self::presentFields($data),
        );
    }

    /**
     * Return page attributes for repository persistence.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->onlyPresent([
            'title' => $this->title,
            'slug' => $this->slug,
            'footer' => $this->footer,
            'content' => $this->content,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<int, string>
     */
    private static function presentFields(array $data): array
    {
        return array_values(array_intersect(['title', 'slug', 'footer', 'content'], array_keys($data)));
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
