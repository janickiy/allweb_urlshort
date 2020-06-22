<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LinkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'alias' => $this->alias,
            'url' => $this->url,
            'short_url' => isset($this->domain) ? $this->domain->name.'/'.$this->alias : route('link.redirect', $this->alias),
            'title' => $this->title,
            'geo_target' => $this->geo_target,
            'platform_target' => $this->platform_target,
            'disabled' => $this->disabled,
            'public' => $this->public,
            'expiration_url' => $this->expiration_url,
            'clicks' => $this->clicks,
            'user_id' => $this->user_id,
            'space' => $this->space,
            'domain' => $this->domain,
            'ends_at' => $this->ends_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    public function with($request)
    {
        return [
            'status' => 200
        ];
    }
}
