<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use App\Services\ArticleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        return ArticleService::getArticles($request);
    }

    public function page(Request $request)
    {
        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);
        $page = floor($offset / $limit) + 1;

        $query = ArticleService::buildQuery($request);

        $total = $query->count();
        $totalPage = ceil($total / 20);


        return response()->json([
            'currentPage' => $page,
            'totalPages' => $totalPage
        ]);
    }

    public function show(Request $request, Article $article)
    {
        return new ArticleResource($article);
    }

    public function store(StoreArticleRequest $request)
    {

        $user = Auth::user() ? Auth::user() : $request->user;

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

        $article = $user->articles()->create($validated['article']);

        if(count($tagIds) > 0) {
            $article->tags()->sync($tagIds);
        }

        return new ArticleResource($article, $user);
    }

    public function update(UpdateArticleRequest $request, Article $article)
    {
        $user = Auth::user() ? Auth::user() : $request->user;

        $validated = $request->validated()['article'];
        if(isset($validated['title'])) {
            $validated['slug'] = \Str::slug($validated['title']);
        }

        $article->update($validated);

        return new ArticleResource($article, $user);
    }

    public function destroy(Request $request, Article $article)
    {
        $article->tags()->detach();
        $article->delete();
        return response()->noContent();
    }

    public function favorite(Request $request, $slug)
    {
        $user = Auth::user() ? Auth::user() : $request->user;
        $article = Article::where('slug', $slug)->firstOrFail();

        if($user->favoriteArticles()->where('article_id', $article->id)->exists()) {
            return response()->json(['message' => '記事はすでにお気に入りされています'], 422);
        }

        $user->favoriteArticles()->syncWithoutDetaching($article->id);
        $article->favoriteUsers()->syncWithoutDetaching($user->id);

        $tags = $article->tags;

        foreach($tags as $tag) {
            $tag->increment('favorite_count');
        }

        return new ArticleResource($article, $user);
    }

    public function unfavorite(Request $request, $slug)
    {
        $user = Auth::user() ? Auth::user() : $request->user;
        $article = Article::where('slug', $slug)->firstOrFail();

        if(!$user->favoriteArticles()->where('article_id', $article->id)->exists()) {
            return response()->json(['message' => '記事はお気に入りされていません'], 422);
        }

        $user->favoriteArticles()->detach($article->id);
        $article->favoriteUsers()->detach($user->id);

        $tags = $article->tags;

        foreach($tags as $tag) {
            $tag->decrement('favorite_count');
        }

        return new ArticleResource($article, $user);
    }

    public function feed(Request $request)
    {

        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);
        $page = floor($offset / $limit) + 1;

        $user = Auth::user() ? Auth::user() : $request->user;

        $query = QueryBuilder::for(Article::class)
        ->defaultSort('-created_at')->allowedSorts(['title', 'description', 'body'])->whereHas('author', function ($query) use ($user) {
            $query->whereIn('id', $user->following()->pluck('id'));
        });

        $articles = $query->paginate($limit, ['*'], 'page', $page);


        return new ArticleCollection($articles, $user);
    }
}
