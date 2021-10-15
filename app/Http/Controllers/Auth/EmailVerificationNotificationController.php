<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Http\JsonResponse;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Handle the incoming request.
     * 
     * @return JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        // is user already verified
        if (auth()->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'email already verified'
            ], 200);
        }

        auth()->user()->notify(new EmailVerificationNotification);

        return response()->json([
            'success' => true,
            'message' => 'check email for verification'
        ], 200);
    }
}
