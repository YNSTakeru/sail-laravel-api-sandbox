<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'user.email' => 'required|email',
            'user.password' => 'required'
        ]);

        if(! $token = JWTAuth::attempt($credentials['user'])) {
            return response()->json(['message' => 'Login information invalid'], 401);
        }

        $user = User::where('email', $credentials['user']['email'])->first();

        return new UserResource($user, $token);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'user.username' => 'required|max:255',
            'user.email' => 'required|max:255|email|unique:users,email',
            'user.password' => 'required|min:6'
        ]);

        $validated['user']['password'] = Hash::make($validated['user']['password']);

        $user = User::create($validated['user']);

        $token = JWTAuth::fromUser($user);

        return new UserResource($user, $token);
    }
}
