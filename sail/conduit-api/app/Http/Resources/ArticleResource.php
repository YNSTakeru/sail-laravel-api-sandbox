<?php

namespace App\Http\Resources;

use App\Models\Article;
use App\Models\FavoriteArticle;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray($request): array
    {
        // 認証しているユーザーかどうか
        $isAuthenticated = auth()->check();
        $favorited = false;

        if ($isAuthenticated) {
            $favorited = $this->favoriteUsers->contains(auth()->user());
        }

        return [
            "slug" => \Str::slug($this->title),
            "title" => $this->title,
            "description" => $this->description,
            "body" => $this->body,
            "tagList" => $this->tags->pluck("name"),
            "createdAt" => $this->created_at,
            "updatedAt" => $this->updated_at,
            "favorited" => $favorited,
            "favoritesCount"=> $this->favoriteUsers->count(),
            "author" => [
                "username" => $this->author->username,
                "bio" => $this->author->bio,
                "image" => $this->author->image,
            ],
        ];
    }
}
