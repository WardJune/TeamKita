<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\EmailVerificationRequest as AuthEmailVerificationRequest;
use Illuminate\Foundation\Http\FormRequest;


class EmailVerificationRequest extends AuthEmailVerificationRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->validateUser();

        if (!hash_equals(
            (string) $this->route('id'),
            (string) $this->user()->getKey()
        )) {
            return false;
        }

        if (!hash_equals(
            (string) $this->route('hash'),
            sha1($this->user()->getEmailForVerification())
        )) {
            return false;
        }

        return true;
    }

    public function validateUser()
    {
        auth()->loginUsingId(User::findOrFail($this->route('id'))->id);

        try {
            if ($this->route('id') != $this->user()->getKey()) {
                throw new AuthorizationException;
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
