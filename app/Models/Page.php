<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Page
 *
 * @mixin Builder
 * @package App
 */
class Page extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'title', 'slug', 'footer', 'content'
    ];

    /**
     * Filter pages by a partial title match.
     *
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeSearch(Builder $query, mixed $value): Builder
    {
        return $query->where('title', 'like', '%' . $value . '%');
    }

    /**
     * Sanitize the page title before storing it.
     *
     * @param $value
     */
    public function setTitleAttribute(mixed $value): void
    {
        $this->attributes['title'] = strip_tags($value);
    }

    /**
     * Sanitize the page URL before storing it.
     *
     * @param $value
     */
    public function setUrlAttribute(mixed $value): void
    {
        $this->attributes['url'] = filter_var(htmlspecialchars(strip_tags($value)));
    }
}
