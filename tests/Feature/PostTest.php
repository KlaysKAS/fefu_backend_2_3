<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\CommentSeeder;
use Database\Seeders\PostSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PostTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
        $this->seed(UserSeeder::class);
        $this->seed(PostSeeder::class);
    }

    public function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    public function test_index()
    {
        $response = $this->getJson('api/posts');
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'title',
                    'slug',
                    'text',
                    'comments' => [
                        '*' => [
                            'author' => [
                                'name',
                                'email'
                            ],
                            'text',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'created_at',
                    'updated_at',
                    'author' => [
                        'name',
                        'email'
                    ]
                ]
            ]);
    }

    public function test_show()
    {
        $response = $this->getJson('api/posts/_');
        $response
            ->assertStatus(404)
            ->assertJson([
                'message' => 'Post not found'
            ]);

        $post = Post::query()->with('user')->first();
        $response = $this->getJson('api/posts/' . $post->slug);
        $response
            ->assertStatus(200)
            ->assertJson([
                'title' => $post->title,
                'slug' => $post->slug,
                'text' => $post->text,
                'author' => [
                    'name' => $post->user->name,
                    'email' => $post->user->email
                ]
            ]);
    }

    public function test_store_guest()
    {
        $post = [
            'title' => 'Guest post',
            'text' => 'Lorem ipsum dolor, maximus laoreet felis.'
        ];

        $request = $this->postJson('api/posts', $post);
        $request
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_store_user()
    {
        $post = [
            'title' => 'User post',
            'text' => 'Lorem ip, maximus laoreet felis.'
        ];
        $request = $this
            ->actingAs(User::factory()->create(), 'sanctum')
            ->postJson('api/posts', $post);
        $request
            ->assertStatus(201)
            ->assertJson([
                'title' => $post['title'],
                'text' => $post['text']
            ]);
    }

    public function test_store_validation() {
        $request = $this
            ->actingAs(User::factory()->create(), 'sanctum')
            ->postJson('api/posts', []);
        $request
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'The title field is required.',
                    'The text field is required.'
                ]
            ]);
    }

    public function test_update_guest() {
        $post = Post::query()->first();
        $request = $this->putJson('api/posts/'.$post->slug, ['text' => 'test']);
        $request
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_update_not_author() {
        $post = Post::query()->first();
        $request = $this
            ->actingAs(User::factory()->create(), 'sanctum')
            ->putJson('api/posts/'.$post->slug, ['text' => 'test']);
        $request
            ->assertStatus(403)
            ->assertJson([
                'message' => 'This action is unauthorized.'
            ]);
    }

    public function test_update_author() {
        $post = Post::query()->with('user')->first();
        $post->text = 'text';
        $request = $this
            ->actingAs($post->user, 'sanctum')
            ->putJson('api/posts/'.$post->slug, ['text' => 'text']);
        $request
            ->assertStatus(200)
            ->assertJson([
                'title' => $post->title,
                'slug' => $post->slug,
                'text' => $post->text,
            ]);
    }

    public function test_update_moderator() {
        $post = Post::query()->first();
        $user = User::factory()->make([
            'role' => Role::MODERATOR
        ]);
        $request = $this
            ->actingAs($user, 'sanctum')
            ->putJson('api/posts/'.$post->slug, ['text' => 'test']);
        $request
            ->assertStatus(200)
            ->assertJson([
                'title' => $post->title,
                'slug' => $post->slug,
                'text' => 'test',
            ]);
    }

    public function test_update_not_exist_slug() {
        $user = User::factory()->make([
            'role' => Role::MODERATOR
        ]);
        $request = $this
            ->actingAs($user, 'sanctum')
            ->putJson('api/posts/_', ['text' => 'test']);
        $request
            ->assertStatus(404)
            ->assertJson([
                'message' => 'Post not found'
            ]);
    }

    public function test_destroy_guest() {
        $post = Post::factory()->create();
        $request = $this->deleteJson('api/posts/'.$post->slug);
        $request
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_destroy_not_author() {
        $post = Post::factory()->create();
        $user = User::factory()->create();
        $request = $this
            ->actingAs($user, 'sanctum')
            ->deleteJson('api/posts/'.$post->slug);
        $request
            ->assertStatus(403)
            ->assertJson([
                'message' => 'This action is unauthorized.'
            ]);
    }

    public function test_destroy_author() {
        $post = Post::factory()->create();
        $user = User::query()->where('id', $post->user_id)->first();
        $request = $this
            ->actingAs($user, 'sanctum')
            ->deleteJson('api/posts/'.$post->slug);
        $request
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Post removed successfully'
            ]);
    }

    public function test_destroy_with_comments() {
        $this->seed(CommentSeeder::class);
        $post = Post::query()->whereHas('comments')->with('user')->first();
        $request = $this
            ->actingAs($post->user, 'sanctum')
            ->deleteJson('api/posts/'.$post->slug);
        $request
            ->assertStatus(403)
            ->assertJson([
                'message' => 'This action is unauthorized.'
            ]);
    }

    public function test_destroy_moderator() {
        $post = Post::query()->first();
        $user = User::factory()->make([
            'role' => Role::MODERATOR
        ]);
        $request = $this
            ->actingAs($user, 'sanctum')
            ->deleteJson('api/posts/'.$post->slug);
        $request
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Post removed successfully'
            ]);
    }

    public function test_destroy_not_exist_slug() {
        $user = User::factory()->make([
            'role' => '1'
        ]);
        $request = $this
            ->actingAs($user, 'sanctum')
            ->deleteJson('api/posts/_');
        $request
            ->assertStatus(404)
            ->assertJson([
                'message' => 'Post not found'
            ]);
    }
}
