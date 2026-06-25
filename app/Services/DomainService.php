<?php

namespace App\Services;

use App\DTO\DomainData;
use App\Models\Domain;
use App\Models\User;
use App\Repositories\DomainRepository;

class DomainService
{
    /**
     * Inject dependencies used by domain operations.
     */
    public function __construct(private readonly DomainRepository $domains)
    {
    }

    /**
     * Create a domain for a user.
     */
    public function create(array $input, User $user)
    {
        return $this->domains->createFromDto(DomainData::fromArray([
            'name' => $this->normalizeName($input['name']),
            'user_id' => $user->id,
            'index_page' => $input['index_page'] ?? null,
            'not_found_page' => $input['not_found_page'] ?? null,
        ]));
    }

    /**
     * Update a domain with normalized input.
     */
    public function update(Domain $domain, array $input): Domain
    {
        $this->domains->updateFromDto($domain->id, DomainData::fromArray([
            'index_page' => $input['index_page'] ?? null,
            'not_found_page' => $input['not_found_page'] ?? null,
        ]));

        return $this->domains->findOrFail($domain->id);
    }

    /**
     * Update a domain owned by a user.
     *
     * @param array<string, mixed> $input
     */
    public function updateForUser(int|string $id, User $user, array $input): Domain
    {
        return $this->update($this->domains->findForUserOrFail($id, $user->id), $input);
    }

    /**
     * Update a domain by primary key for admin workflows.
     *
     * @param array<string, mixed> $input
     */
    public function updateById(int|string $id, array $input): Domain
    {
        return $this->update($this->domains->findOrFail($id), $input);
    }

    /**
     * Delete a domain.
     */
    public function delete(Domain $domain): bool
    {
        return $this->domains->delete($domain->id);
    }

    /**
     * Delete a domain owned by a user and return its display name.
     */
    public function deleteForUser(int|string $id, User $user): string
    {
        return $this->deleteAndReturnName($this->domains->findForUserOrFail($id, $user->id));
    }

    /**
     * Delete a domain by primary key for admin workflows and return its display name.
     */
    public function deleteById(int|string $id): string
    {
        return $this->deleteAndReturnName($this->domains->findOrFail($id));
    }

    /**
     * Return the domain name without a URL scheme.
     */
    public function displayName(Domain $domain): string
    {
        return str_replace(['http://', 'https://'], '', $domain->name);
    }

    /**
     * Normalize a domain name or URL into a host value.
     */
    public function normalizeName(string $value): string
    {
        $host = parse_url($value, PHP_URL_HOST);

        if ($host) {
            return $host;
        }

        return (string) preg_replace('/^https?:\/\//', '', $value);
    }

    /**
     * Delete a domain model and return the name that should be shown to users.
     */
    private function deleteAndReturnName(Domain $domain): string
    {
        $name = $this->displayName($domain);

        $this->delete($domain);

        return $name;
    }
}
