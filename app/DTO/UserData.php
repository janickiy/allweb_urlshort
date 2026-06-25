<?php

namespace App\DTO;

final readonly class UserData implements DataTransferObject
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public mixed $emailVerifiedAt = null,
        public ?string $password = null,
        public ?string $apiToken = null,
        public ?string $locale = null,
        public ?string $timezone = null,
        public ?string $rememberToken = null,
        public ?int $role = null,
        public ?string $stripeId = null,
        public ?string $cardBrand = null,
        public ?string $cardLastFour = null,
        public mixed $trialEndsAt = null,
        public mixed $deletedAt = null,
        private array $fields = [],
    ) {
    }

    /**
     * Create a user DTO from validated input.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: self::nullableString($data['name'] ?? null),
            email: self::nullableString($data['email'] ?? null),
            emailVerifiedAt: $data['email_verified_at'] ?? null,
            password: self::nullableString($data['password'] ?? null),
            apiToken: self::nullableString($data['api_token'] ?? null),
            locale: self::nullableString($data['locale'] ?? null),
            timezone: self::nullableString($data['timezone'] ?? null),
            rememberToken: self::nullableString($data['remember_token'] ?? null),
            role: array_key_exists('role', $data) ? (int) $data['role'] : null,
            stripeId: self::nullableString($data['stripe_id'] ?? null),
            cardBrand: self::nullableString($data['card_brand'] ?? null),
            cardLastFour: self::nullableString($data['card_last_four'] ?? null),
            trialEndsAt: $data['trial_ends_at'] ?? null,
            deletedAt: $data['deleted_at'] ?? null,
            fields: self::presentFields($data),
        );
    }

    /**
     * Return user attributes for repository persistence.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->onlyPresent([
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->emailVerifiedAt,
            'password' => $this->password,
            'api_token' => $this->apiToken,
            'locale' => $this->locale,
            'timezone' => $this->timezone,
            'remember_token' => $this->rememberToken,
            'role' => $this->role,
            'stripe_id' => $this->stripeId,
            'card_brand' => $this->cardBrand,
            'card_last_four' => $this->cardLastFour,
            'trial_ends_at' => $this->trialEndsAt,
            'deleted_at' => $this->deletedAt,
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
            'email',
            'email_verified_at',
            'password',
            'api_token',
            'locale',
            'timezone',
            'remember_token',
            'role',
            'stripe_id',
            'card_brand',
            'card_last_four',
            'trial_ends_at',
            'deleted_at',
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
