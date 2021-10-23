<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CommentResource extends JsonResource
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
            'sub_task_id' => $this->sub_task_id,
            'created_at' => $this->created_at->format('d-m-Y H:i:s'),
            'comment' => $this->comment,
            'author' => [
                'id' => $this->author->id,
                'name' => $this->author->name,
                'username' => $this->author->username,
                'avatar' => Storage::url($this->author->avatar)
            ]
        ];
    }
}
