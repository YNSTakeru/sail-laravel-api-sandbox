<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
