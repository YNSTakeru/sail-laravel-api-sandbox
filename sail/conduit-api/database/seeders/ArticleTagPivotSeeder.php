<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleTagPivotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = Article::all();

        foreach($articles as $article)
        {
            $tags = Tag::inRandomOrder()->take(rand(1, 3))->get();

            foreach($tags as $tag)
            {
                $article->tags()->attach([$tag->id]);
            }
        }
    }
}
