<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubTaskResource extends JsonResource
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
            'task_id' => $this->task_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'status' => $this->status,
            'date_start' => $this->date_start->format('d-m-y H:i:s'),
            'date_end' => $this->date_end->format('d-m-y H:i:s'),
            'created_at' => $this->created_at->format('d-m-y H:i:s'),
            'author' => [
                'id' => $this->author->id,
                'name' => $this->author->name,
                'username' => $this->author->username,
            ],
            'member_count' => $this->members_count,
            'members' => UserResource::collection($this->whenLoaded('members'))
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'message' => 'get sub task specific data'
        ];
    }
}
