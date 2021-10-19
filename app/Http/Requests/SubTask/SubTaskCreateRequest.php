<?php

namespace App\Http\Requests\SubTask;

use App\Http\Requests\CustomFormRequest;

class SubTaskCreateRequest extends CustomFormRequest
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
            'title' => 'required|string',
            'task_id' => 'required|exists:tasks,id',
            'date_start' => 'required|date',
            'date_end' => 'required|date',
            'member_id' => 'required'
        ];
    }
}
