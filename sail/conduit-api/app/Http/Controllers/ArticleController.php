<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleController extends Controller
{
    public function index(Request $request)
    {

        $limit = $request->get("limit", 20);
        $offset = $request->get("offset", 0);

        $page = floor($offset / $limit) + 1;

        $articles = QueryBuilder::for(Article::class)
            ->defaultSort("-created_at")->allowedSorts(["title", "description", "body"])->paginate($limit, ["*"], "page", $page);

        return new ArticleCollection($articles);
    }

    public function show(Request $request, Article $article)
    {
        return new ArticleResource($article);
    }

    public function store(StoreArticleRequest $request)
    {
        $validated = $request->validated();
        $validated["slug"] = \Str::slug($validated["title"]);


        $article = Auth::user()->articles()->create($validated);

        return new ArticleResource($article);
    }

    public function update(UpdateArticleRequest $request, Article $article)
    {
        $validated = $request->validated();

        $article->update($validated);

        return new ArticleResource($article);
    }

    public function destroy(Request $request, Article $article)
    {
        $article->delete();

        return response()->noContent();
    }

    public function updateFavorite(Request $request, $id)
    {
        $user = Auth::user();
        $article = Article::find($id);
        $flag = "exists";

        if($user->favoriteArticles()->where("article_id", $article->id)->exists()){
            $user->favoriteArticles()->detach($article->id);
            $article->favoriteUsers()->detach($user->id);
        }else{
            $flag = "not exists";
            $user->favoriteArticles()->attach($article->id);
            $article->favoriteUsers()->attach($user->id);
        }

        return response()->json([$flag]);
    }
}
