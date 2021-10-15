<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\ProfileDeleteRequest;
use App\Http\Requests\Profile\ProfilePasswordRequest;
use App\Http\Requests\Profile\ProfileUpdateRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Get auth user info
     * 
     * @return UserResource
     */
    public function show(): UserResource
    {
        return new UserResource(auth()->user());
    }

    /**
     * Update auth user
     * 
     * @param ProfileUpdateRequest $request
     * 
     * @return JsonResponse
     */
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $validate = $request->validated();
        $user = auth()->user();

        if ($request->hasFile('avatar')) {
            //refactoring soon
            if ($user->avatar != 'user/avatar/default.jpg' && $user->avatar != null) {
                Storage::delete($user->avatar);
            }

            $file = $validate['avatar'];
            $filename = time() . Str::random(6) . '.' . $file->extension();
            $file->storeAs('user/avatar', $filename);
            $validate['avatar'] = 'user/avatar/' . $filename;
        } else {
            $validate['avatar'] = $user->avatar;
        }

        $user->update($validate);

        return response()->json([
            'success' => true,
            'message' => 'user profile succesfully updated'
        ], 200);
    }

    /**
     * Change password request
     * 
     * @param ProfilePasswordRequest $request
     * 
     * @return JsonResponse
     */
    public function changePassword(ProfilePasswordRequest $request): JsonResponse
    {
        $validate = $request->validated();

        $current_password = Hash::check($validate['current_password'], auth()->user()->password);

        if (!$current_password) {
            return response()->json([
                'success' => false,
                'message' => 'The given data was invalid',
                'error' => [
                    'current_password' => ["current password doesn't match our record"]
                ]
            ], 422);
        }

        if ($validate['current_password'] === $validate['password']) {
            return response()->json([
                'success' => false,
                'message' => 'password cannot be the same as current password'
            ]);
        }

        auth()->user()->update(['password' => Hash::make($validate['password'])]);

        return response()->json([
            'succes' => true,
            'message' => 'password succesfully changed'
        ]);
    }

    /**
     * Delete auth user 
     * 
     * @param ProfileDeleteRequest $request
     * 
     * @return JsonResponse
     */
    public function destroy(ProfileDeleteRequest $request): JsonResponse
    {
        $validate = $request->safe()->only(['email']);
        $user = auth()->user();

        if ($validate['email'] != $user->email) {
            return response()->json([
                'success' => 'false',
                'message' => 'Invalid email'
            ], 422);
        }

        try {
            if ($user->avatar != 'user/avatar/default.jpg' && $user->avatar != null) {
                Storage::delete($user->avatar);
            }
            $user->tokens()->delete();

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'user succesfully deleted'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
