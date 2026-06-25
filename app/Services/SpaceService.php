<?php

namespace App\Services;

use App\DTO\SpaceData;
use App\Models\Space;
use App\Models\User;
use App\Repositories\SpaceRepository;

class SpaceService
{
    public function __construct(private readonly SpaceRepository $spaces)
    {
    }

    public function create(array $input, User $user): Space
    {
        return $this->spaces->createFromDto(SpaceData::fromArray([
            'name' => $input['name'],
            'user_id' => $user->id,
            'color' => $this->color($input['color'] ?? null, 1),
        ]));
    }

    public function update(Space $space, array $input): Space
    {
        $this->spaces->updateFromDto($space->id, SpaceData::fromArray([
            'name' => $input['name'],
            'color' => $this->color($input['color'] ?? null, 0),
        ]));

        return $space->refresh();
    }

    public function delete(Space $space): bool
    {
        return (bool) $space->delete();
    }

    private function color(mixed $color, int $default): int
    {
        return array_key_exists($color, formatSpace()) ? (int) $color : $default;
    }
}
