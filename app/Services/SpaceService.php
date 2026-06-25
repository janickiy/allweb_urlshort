<?php

namespace App\Services;

use App\DTO\SpaceData;
use App\Models\Space;
use App\Models\User;
use App\Repositories\SpaceRepository;

class SpaceService
{
    /**
     * Inject dependencies used by space operations.
     */
    public function __construct(private readonly SpaceRepository $spaces)
    {
    }

    /**
     * Create a user space from input data.
     */
    public function create(array $input, User $user)
    {
        return $this->spaces->createFromDto(SpaceData::fromArray([
            'name' => $input['name'],
            'user_id' => $user->id,
            'color' => $this->color($input['color'] ?? null, 1),
        ]));
    }

    /**
     * Update a space from input data.
     */
    public function update(Space $space, array $input)
    {
        $this->spaces->updateFromDto($space->id, SpaceData::fromArray([
            'name' => $input['name'],
            'color' => $this->color($input['color'] ?? null, 0),
        ]));

        return $this->spaces->findOrFail($space->id);
    }

    /**
     * Update a space owned by a user
     *
     * @param int|string $id
     * @param User $user
     * @param array $input
     * @return Space
     */
    public function updateForUser(int|string $id, User $user, array $input)
    {
        return $this->update($this->spaces->findForUserOrFail($id, $user->id), $input);
    }

    /**
     * Update a space by primary key for admin workflows.
     *
     * @param array<string, mixed> $input
     */
    public function updateById(int|string $id, array $input)
    {
        return $this->update($this->spaces->findOrFail($id), $input);
    }

    /**
     * Delete a space.
     */
    public function delete(Space $space): bool
    {
        return $this->spaces->delete($space->id);
    }

    /**
     * Delete a space owned by a user and return its display nam
     *
     * @param int|string $id
     * @param User $user
     * @return string
     */
    public function deleteForUser(int|string $id, User $user): string
    {
        return $this->deleteAndReturnName($this->spaces->findForUserOrFail($id, $user->id));
    }

    /**
     * Delete a space by primary key for admin workflows and return its display name.
     *
     * @param int|string $id
     * @return string
     */
    public function deleteById(int|string $id): string
    {
        return $this->deleteAndReturnName($this->spaces->findOrFail($id));
    }


    /**
     * Normalize a color value with a default fallback.
     *
     * @param int|string|null $color
     * @param int $default
     * @return int
     */
    private function color(int|string|null $color, int $default): int
    {
        return array_key_exists($color, formatSpace()) ? (int) $color : $default;
    }

    /**
     * Delete a space model and return the name that should be shown to users.
     */
    private function deleteAndReturnName(Space $space): string
    {
        $name = $space->name;

        $this->delete($space);

        return $name;
    }
}
