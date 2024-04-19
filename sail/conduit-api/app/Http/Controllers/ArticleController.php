<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request){
    return new ArticleCollection(Article::all());
    }

    public function show(Request $request, Article $article){
        return new ArticleResource($article);
    }

    public function store(StoreArticleRequest $request){
        $validated = $request->validated();

        $article = Article::create($validated);

        return new ArticleResource($article);
    }

    public function update(UpdateArticleRequest $request, Article $article){
        $validated = $request->validated();

        $article->update($validated);

        return new ArticleResource($article);
    }
}
