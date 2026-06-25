<?php

namespace App\Services;

use App\DTO\DomainData;
use App\Models\Domain;
use App\Models\User;
use App\Repositories\DomainRepository;

class DomainService
{
    public function __construct(private readonly DomainRepository $domains)
    {
    }

    public function create(array $input, User $user): Domain
    {
        return $this->domains->createFromDto(DomainData::fromArray([
            'name' => $this->normalizeName($input['name']),
            'user_id' => $user->id,
            'index_page' => $input['index_page'] ?? null,
            'not_found_page' => $input['not_found_page'] ?? null,
        ]));
    }

    public function update(Domain $domain, array $input): Domain
    {
        $this->domains->updateFromDto($domain->id, DomainData::fromArray([
            'index_page' => $input['index_page'] ?? null,
            'not_found_page' => $input['not_found_page'] ?? null,
        ]));

        return $domain->refresh();
    }

    public function delete(Domain $domain): bool
    {
        return (bool) $domain->delete();
    }

    public function normalizeName(string $value): string
    {
        $host = parse_url($value, PHP_URL_HOST);

        if ($host) {
            return $host;
        }

        return (string) preg_replace('/^https?:\/\//', '', $value);
    }
}
