<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show($id)
    {

        $loggedInUser = Auth::guard('api')->user();


        $user = User::where('username', $id)->firstOrFail();

        if($loggedInUser) {
            // $loggedInUserが$userをフォローしているかどうか
            $following = $loggedInUser->following()->find($user->id);
            if($following) {
                return new ProfileResource($user, true);
            }
        }

        return new ProfileResource($user, false);
    }

    public function follow($id)
    {
        $userToFollow  = User::where('username', $id)->firstOrFail();

        if($userToFollow->id === auth()->id()) {
            return response()->json(['message' => '自分自身はフォローできません。'], 400);
        }

        $user = auth()->user();

        if($user->following()->find($userToFollow->id)) {
            return response()->json(['message' => '既にフォローしています。'], 400);
        }

        $user->following()->syncWithoutDetaching([$userToFollow->id]);
        $userToFollow->followers()->syncWithoutDetaching([$user->id]);

        return new ProfileResource($userToFollow, true);
    }

    public function unfollow($id)
    {
        $userToUnfollow = User::where('username', $id)->firstOrFail();

        $user = auth()->user();


        if(!$user->following()->find($userToUnfollow->id)) {
            return response()->json(['message' => 'フォローしていません。'], 400);
        }

        $user->following()->detach([$userToUnfollow->id]);
        $userToUnfollow->followers()->detach([$user->id]);

        return new ProfileResource($userToUnfollow, false);
    }
}
