<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    /**
     * Login registered user
     * 
     * @param LoginRequest $request
     * 
     * @return JsonResponse
     */
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $validate = $request->safe()->only(['email', 'password']);

        if (!auth()->attempt($validate)) {
            return response()->json([
                'success' => false,
                'message' => 'The given data was invalid',
                'errors' => [
                    'password' => ['Invalid credential']
                ]
            ], 422);
        }

        $user = User::whereEmail($validate['email'])->firstOrFail();
        $token = $user->createToken('auth_token', ['*'])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'user successfully login',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 200);
    }
}
