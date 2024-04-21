<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleController extends Controller
{
    public function index(Request $request)
    {

        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);

        $page = floor($offset / $limit) + 1;

        $articles = QueryBuilder::for(Article::class)
            ->defaultSort('-created_at')->allowedSorts(['title', 'description', 'body'])->paginate($limit, ['*'], 'page', $page);

        return new ArticleCollection($articles);
    }

    public function show(Request $request, Article $article)
    {
        return new ArticleResource($article);
    }

    public function store(StoreArticleRequest $request)
    {
        $validated = $request->validated();
        $validated['slug'] = \Str::slug($validated['title']);
        $tagList = $validated['tagList'];

        foreach ($tagList as $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $tagIds[] = $tag->id;
        }

        unset($validated['tagList']);

        $article = Auth::user()->articles()->create($validated);

        $article->tags()->sync($tagIds);

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

        if($user->favoriteArticles()->where('article_id', $article->id)->exists()) {
            $user->favoriteArticles()->detach($article->id);
            $article->favoriteUsers()->detach($user->id);
        } else {
            $user->favoriteArticles()->syncWithoutDetaching($article->id);
            $article->favoriteUsers()->syncWithoutDetaching($user->id);
        }

        return response()->noContent();
    }
}
