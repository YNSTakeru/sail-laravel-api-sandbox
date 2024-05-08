<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'body',
        'slug'
    ];

    protected $hidden = [
        'updated_at'
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, ArticleTagPivot::class, 'article_id', 'tag_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function favoriteUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, UserFavoriteArticlePivot::class, 'article_id', 'user_id');
    }
}
