<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index(Request $request, $id)
    {
        $article = Article::where('slug', $id)->firstOrFail();
        $comments = $article->comments;


        return new CommentCollection($comments);
    }
    public function store(StoreCommentRequest $request, $id)
    {
        $validated = $request->validated()['comment'];

        $article = Article::where('slug', $id)->firstOrFail();

        $article->comments()->create([
            'body' => $validated['body'],
            'user_id' => auth()->id()
        ]);

        $comment = $article->comments()->latest()->first();


        return new CommentResource($comment);
    }

    public function destroy(Request $request, $slug, $id)
    {
        $article = Article::where('slug', $slug)->firstOrFail();
        $comment = $article->comments()->where('id', $id)->firstOrFail();
        $comment->delete();
        return response()->noContent();
    }
}
