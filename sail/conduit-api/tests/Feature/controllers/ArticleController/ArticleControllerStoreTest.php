<?php

namespace Tests\Feature\ArticleController;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

use function PHPUnit\Framework\assertTrue;

class ArticleControllerStoreTest extends TestCase
{
    use RefreshDatabase;
    public function testCanCreateArticle(): void
    {

        $user = User::factory()->create();
        $tag = Tag::factory()->create();

        $token = JWTAuth::fromUser($user);


        $requestData = [
            'article' => [
                'title' => 'Test Title',
                'description' => 'Test Abstract',
                'body' => 'Test Content',
                'tagList' => [$tag->name],
            ],
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson('/api/articles', $requestData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'article' => [
                'slug',
                'title',
                'description',
                'body',
                'tagList',
                'createdAt',
                'updatedAt',
                'favorited',
                'favoritesCount',
                'author' => [
                    'username',
                    'bio',
                    'image',
                    'following',
                ],
            ],
        ]);

        $this->assertDatabaseHas('articles', [
             'title' => 'Test Title',
             'description' => 'Test Abstract',
             'body' => 'Test Content',
             'author_id' => $user->id,
         ]);

        $this->assertDatabaseHas('tags', [
            'name' => $tag->name,
        ]);

        $this->assertDatabaseHas('article_tag_pivot', [
            'article_id' => Article::first()->id,
            'tag_id' => $tag->id,
        ]);
    }

    public function testTitleIsRequired()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $requestData = [
            'article' => [
                'description' => 'Test Description',
                'body' => 'Test Body',
                'tagList' => ['Test Tag'],
            ],
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson('/api/articles', $requestData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('article.title');
    }

    public function testDescriptionIsRequired()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $requestData = [
            'article' => [
                'title' => 'Test Title',
                'body' => 'Test Body',
                'tagList' => ['Test Tag'],
            ],
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson('/api/articles', $requestData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('article.description');
    }

    public function testBodyIsRequired()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $requestData = [
            'article' => [
                'title' => 'Test Title',
                'description' => 'Test Description',
                'tagList' => ['Test Tag'],
            ],
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson('/api/articles', $requestData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('article.body');
    }

    public function testTagListIsNotRequired()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $requestData = [
            'article' => [
                'title' => 'Test Title',
                'description' => 'Test Description',
                'body' => 'Test Body',
            ],
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson('/api/articles', $requestData);

        $response->assertStatus(201);
    }

    public function testStoreByUnauthenticatedUser()
    {
        $requestData = [
            'article' => [
                'title' => 'Test Title',
                'description' => 'Test Description',
                'body' => 'Test Body',
                'tagList' => ['Test Tag'],
            ],
        ];

        $response = $this->postJson('/api/articles', $requestData);

        $response->assertStatus(401);
    }
}
