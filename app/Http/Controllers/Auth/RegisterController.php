<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Register New user
     * 
     * @param RegisterRequest $request
     * 
     * @return JsonResponse
     */
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $validate = $request->validated();

        try {
            $user = User::create([
                'name' => $validate['name'],
                'username' => $validate['username'],
                'email' => $validate['email'],
                'password' => Hash::make($validate['password'])
            ]);

            $token = $user->createToken('auth_token', ['*'])->plainTextToken;

            $user->notify(new EmailVerificationNotification);

            return response()->json([
                'success' => true,
                'message' => 'user succesfully registered',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 201);
        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
