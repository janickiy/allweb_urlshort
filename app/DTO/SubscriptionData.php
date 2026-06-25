<?php

namespace App\DTO;

final readonly class SubscriptionData implements DataTransferObject
{
    public function __construct(
        public ?int $userId = null,
        public ?string $name = null,
        public ?string $stripeId = null,
        public ?string $stripeStatus = null,
        public ?string $stripePlan = null,
        public ?int $quantity = null,
        public mixed $trialEndsAt = null,
        public mixed $endsAt = null,
        private array $fields = [],
    ) {
    }

    /**
     * Create a subscription DTO from input data.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            userId: array_key_exists('user_id', $data) ? (int) $data['user_id'] : null,
            name: self::nullableString($data['name'] ?? null),
            stripeId: array_key_exists('stripe_id', $data) ? (string) $data['stripe_id'] : null,
            stripeStatus: self::nullableString($data['stripe_status'] ?? null),
            stripePlan: self::nullableString($data['stripe_plan'] ?? null),
            quantity: array_key_exists('quantity', $data) ? (int) $data['quantity'] : null,
            trialEndsAt: $data['trial_ends_at'] ?? null,
            endsAt: $data['ends_at'] ?? null,
            fields: self::presentFields($data),
        );
    }

    /**
     * Return subscription attributes for repository persistence.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->onlyPresent([
            'user_id' => $this->userId,
            'name' => $this->name,
            'stripe_id' => $this->stripeId,
            'stripe_status' => $this->stripeStatus,
            'stripe_plan' => $this->stripePlan,
            'quantity' => $this->quantity,
            'trial_ends_at' => $this->trialEndsAt,
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
            'name',
            'stripe_id',
            'stripe_status',
            'stripe_plan',
            'quantity',
            'trial_ends_at',
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
