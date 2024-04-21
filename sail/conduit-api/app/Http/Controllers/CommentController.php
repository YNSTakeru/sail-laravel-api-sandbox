<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request)
    {
        $validated = $request->validated();

        return response()->json(['comment' => $validated]);

        // $comment = new Comment();
        // $comment->body = $request->input('comment.body');
        // $comment->user_id = auth()->id();
        // $comment->article_id = $request->input('comment.article_id');
        // $comment->save();

        // return response()->json(['comment' => $comment]);
    }
}
