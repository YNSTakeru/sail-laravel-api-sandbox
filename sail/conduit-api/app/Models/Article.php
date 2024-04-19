<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        "title",
        "description",
        "body"
    ];

    protected $hidden = [
        'updated_at'
    ];

    public function getRouteKeyName(): string
    {
        return 'title';
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
