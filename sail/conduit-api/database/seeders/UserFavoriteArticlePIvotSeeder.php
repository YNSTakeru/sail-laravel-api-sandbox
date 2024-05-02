<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\FavoriteArticle;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserFavoriteArticlePIvotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach($users as $user) {
            $articles = Article::inRandomOrder()->take(rand(1, Article::count()))->get();
            foreach($articles as $article) {
                $user->favoriteArticles()->attach([$article->id]);
            }
        }

        $articles = Article::all();

        foreach($articles as $article) {
            $users = User::inRandomOrder()->take(rand(1, User::count()))->get();
            foreach($users as $user) {
                $article->favoriteUsers()->syncWithoutDetaching([$user->id]);

                $tags = $article->tags;
                foreach($tags as $tag) {
                    $tag->increment('favorite_count', $article->favoriteUsers()->count());
                }
            }
        }
    }
}
