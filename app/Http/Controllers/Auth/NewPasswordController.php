<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\NewPasswordRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NewPasswordController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function __invoke(NewPasswordRequest $request): JsonResponse
    {
        $validate = $request->validated();

        $user_reset = DB::table('password_resets')->whereEmail($validate['email']);

        if (!$user_reset->first()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Token'
            ], 404);
        } else {
            $user = $user_reset->first();
            $token = Hash::check($validate['token'], $user->token);
            // is token expired
            $expired = Carbon::createFromTimeString($user->created_at)->diffInMinutes(now()) >= 60;

            if (!$token || $expired) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Token / Expired Token'
                ], 404);
            }
        }

        User::whereEmail($validate['email'])
            ->update(['password' => Hash::make($validate['password'])]);

        // delete record from password_resets
        $user_reset->delete();

        return response()->json([
            'success' => true,
            'message' => 'password succesfully changed'
        ], 200);
    }
}
