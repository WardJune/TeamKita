<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
     * Delete all token from auth user
     * 
     * @return JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        $user = auth()->user();
        $user->tokens()->delete();
        return response()->json([
            'success' => true,
            'message' => 'user succesfully logged out'
        ], 200);
    }
}
