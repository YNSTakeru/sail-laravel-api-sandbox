<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileResource;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show($id)
    {
        $user = User::where('username', $id)->firstOrFail();
        return new ProfileResource($user);
    }

    public function follow($id)
    {
        $userToFollow  = User::where('username', $id)->firstOrFail();

        if($userToFollow->id === auth()->id()) {
            return response()->json(['message' => '自分自身はフォローできません。'], 400);
        }

        $user = auth()->user();

        if($user->followers()->find($userToFollow->id)) {
            return response()->json(['message' => '既にフォローしています。'], 400);
        }

        $user->followers()->attach([$userToFollow->id]);
        return new ProfileResource($user, $userToFollow->id);
    }
}
