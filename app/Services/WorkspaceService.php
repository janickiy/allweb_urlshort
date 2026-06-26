<?php

namespace App\Services;

use App\DTO\WorkspaceData;
use App\Models\Workspace;
use App\Models\User;
use App\Repositories\WorkspaceRepository;

class WorkspaceService
{
    /**
     * Inject dependencies used by workspace operations.
     */
    public function __construct(private readonly WorkspaceRepository $workspaces)
    {
    }

    /**
     * Create a user workspace from input data.
     */
    public function create(array $input, User $user): Workspace
    {
        return $this->workspaces->createFromDto(WorkspaceData::fromArray([
            'name' => $input['name'],
            'user_id' => $user->id,
            'color' => $this->color($input['color'] ?? null, 1),
        ]));
    }

    /**
     * Update a workspace from input data.
     */
    public function update(Workspace $workspace, array $input): Workspace
    {
        $this->workspaces->updateFromDto($workspace->id, WorkspaceData::fromArray([
            'name' => $input['name'],
            'color' => $this->color($input['color'] ?? null, 0),
        ]));

        return $this->workspaces->findOrFail($workspace->id);
    }

    /**
     * Update a workspace owned by a user
     *
     * @param int|string $id
     * @param User $user
     * @param array $input
     * @return Workspace
     */
    public function updateForUser(int|string $id, User $user, array $input): Workspace
    {
        return $this->update($this->workspaces->findForUserOrFail($id, $user->id), $input);
    }

    /**
     * Update a workspace by primary key for admin workflows.
     *
     * @param array<string, mixed> $input
     */
    public function updateById(int|string $id, array $input): Workspace
    {
        return $this->update($this->workspaces->findOrFail($id), $input);
    }

    /**
     * Delete a workspace.
     */
    public function delete(Workspace $workspace): bool
    {
        return $this->workspaces->delete($workspace->id);
    }

    /**
     * Delete a workspace owned by a user and return its display name.
     *
     * @param int|string $id
     * @param User $user
     * @return string
     */
    public function deleteForUser(int|string $id, User $user): string
    {
        return $this->deleteAndReturnName($this->workspaces->findForUserOrFail($id, $user->id));
    }

    /**
     * Delete a workspace by primary key for admin workflows and return its display name.
     *
     * @param int|string $id
     * @return string
     */
    public function deleteById(int|string $id): string
    {
        return $this->deleteAndReturnName($this->workspaces->findOrFail($id));
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
        return array_key_exists($color, formatWorkspace()) ? (int) $color : $default;
    }

    /**
     * Delete a workspace model and return the name that should be shown to users.
     */
    private function deleteAndReturnName(Workspace $workspace): string
    {
        $name = $workspace->name;

        $this->delete($workspace);

        return $name;
    }
}
