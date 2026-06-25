<?php

namespace App\DTO;

abstract readonly class ArrayDataTransferObject implements DataTransferObject
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(private array $attributes)
    {
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public static function fromArray(array $attributes): static
    {
        return new static($attributes);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
