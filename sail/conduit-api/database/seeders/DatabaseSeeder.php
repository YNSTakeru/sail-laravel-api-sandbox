<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Article;
use App\Models\ArticleTagPivot;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(UserSeeder::class);

        User::factory(10)->create();
        Article::factory(600)->create();
        Tag::factory(50)->create();

        $this->call([ArticleTagPivotSeeder::class, UserFavoriteArticlePIvotSeeder::class]);
    }
}
