<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleController extends Controller
{
    public function index(Request $request)
    {

        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);
        $tag = $request->get('tag');
        $author = $request->get('author');
        $favorited = $request->get('favorited');

        $page = floor($offset / $limit) + 1;

        $query = QueryBuilder::for(Article::class)
            ->defaultSort('-created_at')->allowedSorts(['title', 'description', 'body']);

        if($tag) {
            $query->whereHas('tags', function ($query) use ($tag) {
                $query->where('name', $tag);
            });
        }

        if($author) {
            $query->whereHas('author', function ($query) use ($author) {
                $query->where('username', $author);
            });
        }

        if($favorited) {
            $user = User::where('username', $favorited)->first();
            if($user) {
                $query->whereHas('favoriteUsers', function ($query) use ($user) {
                    $query->where('id', $user->id);
                });
            }
        }

        $articles = $query->paginate($limit, ['*'], 'page', $page);


        return new ArticleCollection($articles);
    }

    public function show(Request $request, Article $article)
    {
        return new ArticleResource($article);
    }

    public function store(StoreArticleRequest $request)
    {
        $validated = $request->validated();
        $validated['article']['slug'] = \Str::slug($validated['article']['title']);
        $tagIds = [];

        if (isset($validated['article']['tagList'])) {
            $tagList = $validated['article']['tagList'];

            foreach ($tagList as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
        }

        unset($validated['article']['tagList']);

        $article = Auth::user()->articles()->create($validated['article']);

        if(count($tagIds) > 0) {
            $article->tags()->sync($tagIds);
        }

        return new ArticleResource($article);
    }

    public function update(UpdateArticleRequest $request, Article $article)
    {
        $validated = $request->validated()['article'];
        $validated['slug'] = \Str::slug($validated['title']);

        $article->update($validated);

        return new ArticleResource($article);
    }

    public function destroy(Request $request, Article $article)
    {
        $article->tags()->detach();
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

    public function favorite(Request $request, $slug)
    {
        $user = Auth::user();
        $article = Article::where('slug', $slug)->firstOrFail();

        if($user->favoriteArticles()->where('article_id', $article->id)->exists()) {
            return response()->json(['message' => '記事はすでにお気に入りされています'], 422);
        }

        $user->favoriteArticles()->syncWithoutDetaching($article->id);
        $article->favoriteUsers()->syncWithoutDetaching($user->id);

        return new ArticleResource($article);
    }

    public function unfavorite(Request $request, $slug)
    {
        $user = Auth::user();
        $article = Article::where('slug', $slug)->firstOrFail();

        if(!$user->favoriteArticles()->where('article_id', $article->id)->exists()) {
            return response()->json(['message' => '記事はお気に入りされていません'], 422);
        }

        $user->favoriteArticles()->detach($article->id);
        $article->favoriteUsers()->detach($user->id);

        return new ArticleResource($article);
    }

    public function feed(Request $request)
    {

        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);
        $page = floor($offset / $limit) + 1;

        $user = Auth::user();

        $query = QueryBuilder::for(Article::class)
        ->defaultSort('-created_at')->allowedSorts(['title', 'description', 'body'])->whereHas('author', function ($query) use ($user) {
            $query->whereIn('id', $user->following()->pluck('id'));
        });

        $articles = $query->paginate($limit, ['*'], 'page', $page);


        return new ArticleCollection($articles);
    }
}
