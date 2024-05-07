<?php

namespace App\Services;

use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Article;
use App\Models\User;
use App\Http\Resources\ArticleCollection;
use Illuminate\Support\Facades\Auth;

class ArticleService
{

    public static function getArticles(Request $request)
    {
        $loggedInUser = Auth::user();

        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);
        $page = floor($offset / $limit) + 1;

        $query = self::buildQuery($request);

        $articles = $query->paginate($limit, ['*'], 'page', $page);

        if($request->get('tag')) {
            self::sortArticlesByTag($articles, $request->get('tag'));
        }

        return new ArticleCollection($articles, $loggedInUser);
    }
    public static function buildQuery(Request $request)
    {
        $query = QueryBuilder::for(Article::class)
        ->defaultSort('-created_at')->allowedSorts(['title', 'description', 'body']);

        if($request->get('tag')) {
            $query->whereHas('tags', function ($query) use ($request) {
                $query->where('name', $request->get('tag'));
            });
        }

        if($request->get('author')) {
            $query->whereHas('author', function ($query) use ($request) {
                $query->where('username', $request->get('author'));
            });
        }

        if($request->get('favorited')) {
            $user = User::where('username', $request->get('favorited'))->first();
            if($user) {
                $query->whereHas('favoriteUsers', function ($query) use ($user) {
                    $query->where('id', $user->id);
                });
            }
        }

        return $query;
    }

    private static function sortArticlesByTag($articles, $tag)
    {
        foreach($articles as $article) {
            $article->tags = $article->tags->sortByDesc(function ($t) use ($tag) {
                return $t->name === $tag ? 1 : 0;
            })->values();
        }
    }
}
