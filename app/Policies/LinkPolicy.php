<?php

namespace App\Policies;

use App\Traits\UserFeaturesTrait;
use App\User;
use App\Link;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class LinkPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any links.
     *
     * @param \App\User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the link.
     *
     * @param \App\User $user
     * @param \App\Link $link
     * @return mixed
     */
    public function view(User $user, Link $link)
    {
        //
    }

    /**
     * Determine whether the user can create links.
     *
     * @param \App\User $user
     * @param $limit
     * @return mixed
     */
    public function create(User $user, $limit)
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
     * @param \App\User $user
     * @param \App\Link $link
     * @return mixed
     */
    public function update(User $user, Link $link)
    {
        //
    }

    /**
     * Determine whether the user can delete the link.
     *
     * @param \App\User $user
     * @param \App\Link $link
     * @return mixed
     */
    public function delete(User $user, Link $link)
    {
        //
    }

    /**
     * Determine whether the user can restore the link.
     *
     * @param \App\User $user
     * @param \App\Link $link
     * @return mixed
     */
    public function restore(User $user, Link $link)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the link.
     *
     * @param \App\User $user
     * @param \App\Link $link
     * @return mixed
     */
    public function forceDelete(User $user, Link $link)
    {
        //
    }

    /**
     * @param User $user
     * @param $limit
     * @return bool
     */
    public function domains(User $user, $limit)
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
    public function spaces(User $user, $limit)
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
    public function stats(User $user, $limit)
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
    public function disabled(User $user, $limit)
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
    public function geo(User $user, $limit)
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
    public function platform(User $user, $limit)
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
    public function utm(User $user, $limit)
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
    public function password(User $user, $limit)
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
    public function expiration(User $user, $limit)
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
    public function api(User $user, $limit)
    {
        if ($limit) {
            return true;
        }

        return false;
    }
}
