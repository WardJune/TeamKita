<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /**
     * Handle the incoming request.
     * 
     * @param ResetPasswordRequest $request
     * 
     * @return JsonResponse
     */
    public function __invoke(ResetPasswordRequest $request): JsonResponse
    {
        $validate = $request->safe()->only('email');

        $user = User::whereEmail($validate['email'])->first();

        //generate token
        $token = Password::getRepository()->create($user);

        $user->notify(new ResetPasswordNotification($token));

        return response()->json([
            'sucess' => true,
            'message' => 'check user email, for reset password'
        ], 200);
    }
}
