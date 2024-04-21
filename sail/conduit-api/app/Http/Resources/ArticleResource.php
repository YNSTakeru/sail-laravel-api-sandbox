<?php

namespace App\Http\Resources;

use App\Models\Article;
use App\Models\FavoriteArticle;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray($request): array
    {
        $user = Auth::guard('api')->user();
        $isAuthenticated = $user ? true : false;
        $favorited = false;

        if ($isAuthenticated) {
           $favorited = $user->favoriteArticles()->where("article_id", $this->id)->exists();
        }

        return [
            "slug" => \Str::slug($this->title),
            "title" => $this->title,
            "description" => $this->description,
            "body" => $this->body,
            "tagList" => $this->tags->pluck("name"),
            "createdAt" => $this->created_at,
            "updatedAt" => $this->updated_at,
            "favorited" => $isAuthenticated,
            "favoritesCount"=> $this->favoriteUsers->count(),
            "author" => [
                "username" => $this->author->username,
                "bio" => $this->author->bio,
                "image" => $this->author->image,
            ],
        ];
    }
}
