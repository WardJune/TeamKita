<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'code' => $this->code,
            'created_at' => $this->created_at->format('d-m-y h:i:s'),
            'author' => [
                'id' => $this->author->id,
                'name' => $this->author->name,
                'username' => $this->author->username,
            ],
            'member' => $this->whenAppended('members', UserResource::collection($this->whenLoaded('members'))),
            'members_count' => $this->whenAppended('count', $this->members_count),

        ];
    }

    public function with($request)
    {
        return [
            'message' => 'get specific data task',
            'success' => true,
        ];
    }
}
