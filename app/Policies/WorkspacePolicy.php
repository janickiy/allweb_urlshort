<?php

namespace App\Policies;

use App\Traits\UserFeaturesTrait;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkspacePolicy
{
    use HandlesAuthorization, UserFeaturesTrait;
    
    /**
     * Determine whether the user can view any workspaces.
     *
     * @param  User  $user
     * @return mixed
     */
    public function viewAny(User $user): mixed
    {
        //
    }

    /**
     * Determine whether the user can view the workspace.
     *
     * @param  User  $user
     * @param  Workspace  $workspace
     * @return mixed
     */
    public function view(User $user, Workspace $workspace): mixed
    {
        //
    }

    /**
     * Determine whether the user can create workspaces.
     *
     * @param  User  $user
     * @return mixed
     */
    public function create(User $user, mixed $limit): bool
    {
        if ($limit == -1) {
            return true;
        } elseif($limit > 0) {
            $count = Workspace::where('user_id', '=', $user->id)->count();

            if ($count < $limit) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can update the workspace.
     *
     * @param  User  $user
     * @param  Workspace  $workspace
     * @return mixed
     */
    public function update(User $user, Workspace $workspace): mixed
    {
        //
    }

    /**
     * Determine whether the user can delete the workspace.
     *
     * @param  User  $user
     * @param  Workspace  $workspace
     * @return mixed
     */
    public function delete(User $user, Workspace $workspace): mixed
    {
        //
    }

    /**
     * Determine whether the user can restore the workspace.
     *
     * @param  User  $user
     * @param  Workspace  $workspace
     * @return mixed
     */
    public function restore(User $user, Workspace $workspace): mixed
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the workspace.
     *
     * @param  User  $user
     * @param  Workspace  $workspace
     * @return mixed
     */
    public function forceDelete(User $user, Workspace $workspace): mixed
    {
        //
    }
}
