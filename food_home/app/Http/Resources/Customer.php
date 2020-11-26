<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Customer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user_info = $this->user;
        return [
            'id' => $this->id,
            'name' => $user_info->name,
            'address' => $user_info->phone,
            'phone' => $this->phone,
            'nif' => $this->nif,
            'email' => $user_info->email,
            'blocked' => $user_info->blocked,
            'photo_url' => $user_info->photo_url,
        ];
    }
}
