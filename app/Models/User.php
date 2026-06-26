<?php

namespace App\Models;

use App\Traits\StaticTableName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Billable;
use Illuminate\Contracts\Translation\HasLocalePreference;

/**
 * Class User
 *
 * @mixin Builder
 * @package App
 */
class User extends Authenticatable implements MustVerifyEmail, HasLocalePreference
{
    use HasFactory, Notifiable, Billable, SoftDeletes, StaticTableName;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role', 'api_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Filter users by a partial name match.
     *
     * @param Builder $query
     * @param string $value
     * @return Builder
     */
    public function scopeSearchName(Builder $query, string $value): Builder
    {
        return $query->where('name', 'like', '%' . $value . '%');
    }

    /**
     * Filter users by a partial email match.
     *
     * @param Builder $query
     * @param string $value
     * @return Builder
     */
    public function scopeSearchEmail(Builder $query, string $value): Builder
    {
        return $query->where('email', 'like', '%' . $value . '%');
    }

    /**
     * Filter users by role.
     *
     * @param Builder $query
     * @param int|string $value
     * @return Builder
     */
    public function scopeSearchRole(Builder $query, int|string $value): Builder
    {
        return $query->where('role', '=', $value);
    }

    /**
     * Get the preferred locale used for notifications and translations.
     *
     * @return string|null
     */
    public function preferredLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * Get the links owned by this user.
     */
    public function links(): HasMany
    {
        return $this->hasMany(Link::class)->where('user_id', $this->id);
    }

    /**
     * Get the workspaces owned by this user.
     */
    public function workspaces(): HasMany
    {
        return $this->hasMany(Workspace::class)->where('user_id', $this->id);
    }

    /**
     * Get the domains owned by this user.
     */
    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class)->where('user_id', $this->id);
    }

    /**
     * Get the click statistics recorded for this user.
     */
    public function stats(): HasMany
    {
        return $this->hasMany(Stat::class)->where('user_id', $this->id);
    }
}
