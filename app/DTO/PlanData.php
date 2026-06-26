<?php

namespace App\DTO;

final readonly class PlanData implements DataTransferObject
{
    public function __construct(
        public ?string $product = null,
        public ?string $name = null,
        public ?string $description = null,
        public ?int $trialDays = null,
        public ?string $currency = null,
        public ?int $decimals = null,
        public ?string $planMonth = null,
        public ?string $planYear = null,
        public ?int $amountMonth = null,
        public ?int $amountYear = null,
        public ?int $visibility = null,
        public ?string $color = null,
        public ?int $optionApi = null,
        public ?int $optionLinks = null,
        public ?int $optionWorkspaces = null,
        public ?int $optionDomains = null,
        public ?int $optionStats = null,
        public ?int $optionGeo = null,
        public ?int $optionPlatform = null,
        public ?int $optionExpiration = null,
        public ?int $optionPassword = null,
        public ?int $optionDisabled = null,
        public ?int $optionUtm = null,
        public mixed $deletedAt = null,
        private array $fields = [],
    ) {
    }

    /**
     * Create a plan DTO from validated input.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            product: self::nullableString($data['product'] ?? null),
            name: self::nullableString($data['name'] ?? null),
            description: array_key_exists('description', $data) ? (string) $data['description'] : null,
            trialDays: array_key_exists('trial_days', $data) ? (int) $data['trial_days'] : null,
            currency: self::nullableString($data['currency'] ?? null),
            decimals: array_key_exists('decimals', $data) && $data['decimals'] !== null ? (int) $data['decimals'] : null,
            planMonth: self::nullableString($data['plan_month'] ?? null),
            planYear: self::nullableString($data['plan_year'] ?? null),
            amountMonth: array_key_exists('amount_month', $data) ? (int) $data['amount_month'] : null,
            amountYear: array_key_exists('amount_year', $data) ? (int) $data['amount_year'] : null,
            visibility: array_key_exists('visibility', $data) ? (int) $data['visibility'] : null,
            color: self::nullableString($data['color'] ?? null),
            optionApi: array_key_exists('option_api', $data) ? (int) $data['option_api'] : null,
            optionLinks: array_key_exists('option_links', $data) ? (int) $data['option_links'] : null,
            optionWorkspaces: array_key_exists('option_workspaces', $data) ? (int) $data['option_workspaces'] : null,
            optionDomains: array_key_exists('option_domains', $data) ? (int) $data['option_domains'] : null,
            optionStats: array_key_exists('option_stats', $data) ? (int) $data['option_stats'] : null,
            optionGeo: array_key_exists('option_geo', $data) ? (int) $data['option_geo'] : null,
            optionPlatform: array_key_exists('option_platform', $data) ? (int) $data['option_platform'] : null,
            optionExpiration: array_key_exists('option_expiration', $data) ? (int) $data['option_expiration'] : null,
            optionPassword: array_key_exists('option_password', $data) ? (int) $data['option_password'] : null,
            optionDisabled: array_key_exists('option_disabled', $data) ? (int) $data['option_disabled'] : null,
            optionUtm: array_key_exists('option_utm', $data) ? (int) $data['option_utm'] : null,
            deletedAt: $data['deleted_at'] ?? null,
            fields: self::presentFields($data),
        );
    }

    /**
     * Return plan attributes for repository persistence.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->onlyPresent([
            'product' => $this->product,
            'name' => $this->name,
            'description' => $this->description,
            'trial_days' => $this->trialDays,
            'currency' => $this->currency,
            'decimals' => $this->decimals,
            'plan_month' => $this->planMonth,
            'plan_year' => $this->planYear,
            'amount_month' => $this->amountMonth,
            'amount_year' => $this->amountYear,
            'visibility' => $this->visibility,
            'color' => $this->color,
            'option_api' => $this->optionApi,
            'option_links' => $this->optionLinks,
            'option_workspaces' => $this->optionWorkspaces,
            'option_domains' => $this->optionDomains,
            'option_stats' => $this->optionStats,
            'option_geo' => $this->optionGeo,
            'option_platform' => $this->optionPlatform,
            'option_expiration' => $this->optionExpiration,
            'option_password' => $this->optionPassword,
            'option_disabled' => $this->optionDisabled,
            'option_utm' => $this->optionUtm,
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
            'product',
            'name',
            'description',
            'trial_days',
            'currency',
            'decimals',
            'plan_month',
            'plan_year',
            'amount_month',
            'amount_year',
            'visibility',
            'color',
            'option_api',
            'option_links',
            'option_workspaces',
            'option_domains',
            'option_stats',
            'option_geo',
            'option_platform',
            'option_expiration',
            'option_password',
            'option_disabled',
            'option_utm',
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
