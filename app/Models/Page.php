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
     * @param string $value
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $value): Builder
    {
        return $query->where('title', 'like', '%' . $value . '%');
    }

    /**
     * Sanitize the page title before storing it.
     *
     * @param string $value
     */
    public function setTitleAttribute(string $value): void
    {
        $this->attributes['title'] = strip_tags($value);
    }

    /**
     * Sanitize the page URL before storing it.
     *
     * @param string $value
     */
    public function setUrlAttribute(string $value): void
    {
        $this->attributes['url'] = filter_var(htmlspecialchars(strip_tags($value)));
    }
}
