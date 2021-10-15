<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailVerificationRequest;
use Illuminate\Auth\Events\Verified;

class VerifyEmailController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            // response json
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'email already verified'
                ], 200);
            }
            return redirect()->to(env('FRONT_END_URL_VERIFIED_ALREADY'));
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        // response json
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'email verified'
            ]);
        }

        return redirect()->to(env('FRONT_END_URL_JUST_VERIFIED'));
    }
}
