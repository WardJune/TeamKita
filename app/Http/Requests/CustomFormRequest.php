<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class CustomFormRequest extends FormRequest
{
  protected function failedValidation(Validator $validator)
  {
    $errors = (new ValidationException($validator))->errors();

    throw new HttpResponseException(
      response()->json([
        'success' => false,
        'message' => "The given data was invalid",
        'errors' => $errors
      ], 422)
    );
  }
}
