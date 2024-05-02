<?php

namespace App\Http\Controllers;

use App\Http\Resources\TagCollection;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::all();

        return new TagCollection($tags);
    }

    public function popular()
    {
        $tags = Tag::orderBy('favorite_count', 'desc')->limit(10)->get();

        return new TagCollection($tags);
    }
}
