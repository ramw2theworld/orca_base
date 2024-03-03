<?php

namespace Modules\User\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Role\Http\Resources\RoleResource;

class UserResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'username' => $this->username,
            'status' => ($this->status===1 or $this->status===true)?'active':'inactive',
            'created_at'=>(new \DateTime($this->created_at))->format('Y-m-d H:i:s'),
            'role'=> new RoleResource($this->whenLoaded('role')),
        ];
    }
}
