<?php

namespace Modules\Permission\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'created_at' => $this->slug,
            'created_at' => \Carbon\Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),

        ];
    }
}
