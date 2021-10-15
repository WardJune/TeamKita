<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\CustomFormRequest;
use App\Models\User;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends CustomFormRequest
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
            'name' => 'required|string',
            'username' => ['required', Rule::unique((new User)->getTable())->ignore(auth()->id())],
            'avatar' => 'image|mimes:jpg,png,jpeg',
        ];
    }
}
