<?php

namespace App\Http\Resources;

use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CommentResource extends JsonResource
{
    public static $wrap = 'comment';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = Auth::guard('api')->user();
        $isAuthenticated = $user ? true : false;

        $author = User::find($this->user_id);

        return [
            'id' => $this->id,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'body' => $this->body,
            'author' => [
                'username' => $author->username,
                'bio' => $author->bio,
                'image' => $author->image,
                'following' => $isAuthenticated ? $user->followers->contains($author->id) : false,
            ]
        ];

    }
}
