<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    public function articles()
    {
        return $this->belongsToMany(Article::class, ArticleTagPivot::class, 'article_id', 'tag_id');
    }
}