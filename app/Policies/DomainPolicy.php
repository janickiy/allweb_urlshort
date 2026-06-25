<?php

namespace App\Policies;

use App\Traits\UserFeaturesTrait;
use App\Models\User;
use App\Models\Domain;
use Illuminate\Auth\Access\HandlesAuthorization;

class DomainPolicy
{
    use HandlesAuthorization, UserFeaturesTrait;

    /**
     * Determine whether the user can view any domains.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function viewAny(User $user): mixed
    {
        //
    }

    /**
     * Determine whether the user can view the domain.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Domain $domain
     * @return mixed
     */
    public function view(User $user, Domain $domain): mixed
    {
        //
    }

    /**
     * Determine whether the user can create domains.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user, mixed $limit): bool
    {
        if ($limit == -1) {
            return true;
        } elseif ($limit > 0) {
            $count = Domain::where('user_id', '=', $user->id)->count();

            if ($count < $limit) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can update the domain.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Domain $domain
     * @return mixed
     */
    public function update(User $user, Domain $domain): mixed
    {
        //
    }

    /**
     * Determine whether the user can delete the domain.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Domain $domain
     * @return mixed
     */
    public function delete(User $user, Domain $domain): mixed
    {
        //
    }

    /**
     * Determine whether the user can restore the domain.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Domain $domain
     * @return mixed
     */
    public function restore(User $user, Domain $domain): mixed
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the domain.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Domain $domain
     * @return mixed
     */
    public function forceDelete(User $user, Domain $domain): mixed
    {
        //
    }
}
