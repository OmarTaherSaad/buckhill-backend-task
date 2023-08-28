<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordFacade;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (auth()->validate($credentials)) {
            $user = User::firstWhere('email', $credentials['email']);
            $token = issueToken($user);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login success',
            'data' => [
                'token' => $token,
                'user' => new UserResource($user),
            ],
        ]);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json([
            'success' => true,
            'message' => 'Logout success',
        ]);
    }

    public function sendPasswordResetToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        //Generate password reset token
        /** @var User */
        $user = User::firstWhere('email', $request->email);
        $token = PasswordFacade::getRepository()->create($user);

        return response()->json([
            'success' => true,
            'message' => 'Password reset token sent',
            'data' => [
                'token' => $token,
            ],
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $status = PasswordFacade::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($status === PasswordFacade::INVALID_TOKEN) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password reset success',
        ]);
    }
}
