<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        // headerのtokenを取得
        $token = str_replace('Bearer ', '', $request->header('Authorization'));

        return new UserResource($user, $token);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $token = str_replace('Bearer ', '', $request->header('Authorization'));

        $validated = $request->validate([
            'user.email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'user.bio' => 'nullable|string',
            'user.image' => 'nullable|url',
        ]);

        if (isset($validated['user']['email'])) {
            $user->email = $validated['user']['email'];
        }

        if (isset($validated['user']['bio'])) {
            $user->bio = $validated['user']['bio'];
        }

        if (isset($validated['user']['image'])) {
            $user->image = $validated['user']['image'];
        }

        $user->save();

        return new UserResource($user, $token);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
