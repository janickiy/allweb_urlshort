<?php

namespace App\Policies;

use App\Traits\UserFeaturesTrait;
use App\Models\User;
use App\Models\Link;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class LinkPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any links.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user): mixed
    {
        //
    }

    /**
     * Determine whether the user can view the link.
     *
     * @param User $user
     * @param Link $link
     * @return mixed
     */
    public function view(User $user, Link $link): mixed
    {
        //
    }

    /**
     * Determine whether the user can create links.
     *
     * @param User $user
     * @param $limit
     * @return mixed
     */
    public function create(User $user, mixed $limit): bool
    {
        if ($limit == -1) {
            return true;
        } elseif ($limit > 0) {
            $count = Link::where('user_id', '=', $user->id)->count();

            if ($count < $limit) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can update the link.
     *
     * @param User $user
     * @param Link $link
     * @return mixed
     */
    public function update(User $user, Link $link): mixed
    {
        //
    }

    /**
     * Determine whether the user can delete the link.
     *
     * @param User $user
     * @param Link $link
     * @return mixed
     */
    public function delete(User $user, Link $link): mixed
    {
        //
    }

    /**
     * Determine whether the user can restore the link.
     *
     * @param User $user
     * @param Link $link
     * @return mixed
     */
    public function restore(User $user, Link $link): mixed
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the link.
     *
     * @param User $user
     * @param Link $link
     * @return mixed
     */
    public function forceDelete(User $user, Link $link): mixed
    {
        //
    }

    /**
     * @param User $user
     * @param $limit
     * @return bool
     */
    public function domains(User $user, mixed $limit): bool
    {
        if ($limit) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param $limit
     * @return bool
     */
    public function spaces(User $user, mixed $limit): bool
    {
        if ($limit) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param $limit
     * @return bool
     */
    public function stats(User $user, mixed $limit): bool
    {
        if ($limit) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param $limit
     * @return bool
     */
    public function disabled(User $user, mixed $limit): bool
    {
        if ($limit) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param $limit
     * @return bool
     */
    public function geo(User $user, mixed $limit): bool
    {
        if ($limit) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param $limit
     * @return bool
     */
    public function platform(User $user, mixed $limit): bool
    {
        if ($limit) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param $limit
     * @return bool
     */
    public function utm(User $user, mixed $limit): bool
    {
        if ($limit) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param $limit
     * @return bool
     */
    public function password(User $user, mixed $limit): bool
    {
        if ($limit) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param $limit
     * @return bool
     */
    public function expiration(User $user, mixed $limit): bool
    {
        if ($limit) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param $limit
     * @return bool
     */
    public function api(User $user, mixed $limit): bool
    {
        if ($limit) {
            return true;
        }

        return false;
    }
}
