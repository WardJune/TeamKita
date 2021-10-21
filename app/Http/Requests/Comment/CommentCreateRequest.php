<?php

namespace App\Http\Requests\Comment;

use App\Http\Requests\CustomFormRequest;

class CommentCreateRequest extends CustomFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'comment' => 'required|string',
            'sub_task_id' => 'required|exists:sub_tasks,id'
        ];
    }
}
