<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
                'message' => 'Login failed',
            ], 401);
        }

        return response()->json([
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
            'message' => 'Logout success',
        ]);
    }
}
