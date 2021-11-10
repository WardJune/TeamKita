<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'avatar' => $this->avatar_path,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at != null ? $this->email_verified_at->format('d-m-Y h:i:s') : null,
            'created_at' => $this->created_at->format('d-m-Y h:i:s')
        ];
    }

    public function with($request)
    {
        return [
            'message' => 'get data user',
            'success' => true,
        ];
    }
}
