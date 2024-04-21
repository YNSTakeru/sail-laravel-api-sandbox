<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProfileResource extends JsonResource
{
    protected $userToFollowId;

    public function __construct($resource, $userToFollowId = null)
    {
        parent::__construct($resource);
        $this->userToFollowId = $userToFollowId;
    }

    public static $wrap = 'profile';
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $following = false;
        $user = Auth::guard('api')->user();
        if ($user) {
            $following = $user->followers->contains($this->userToFollowId) ? true : false;
        }

        return [
            'username' => $this->username,
            'bio' => $this->bio,
            'image' => $this->image,
            'following' => $following,
        ];
    }
}
