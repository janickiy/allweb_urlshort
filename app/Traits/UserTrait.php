<?php


namespace App\Traits;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait UserTrait
{
    /**
     * Update the user
     *
     * @param Request $request
     * @param Model $user
     * @param null $admin
     * @return User|Model
     */
    protected function userUpdate(Request $request, Model $user, $admin = null)
    {
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->timezone = $request->input('timezone');

        if ($admin) {
            $user->role = $request->input('role');
        }

        $user->save();

        return $user;
    }
}