<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Database\Seeders\PostSeeder;
use Database\Seeders\UserSeeder;
use Faker\Provider\Lorem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostTest extends TestCase
{
//    use RefreshDatabase;

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

        $post = Post::query()->first();
        $author = User::query()->where('id', $post->user_id)->first();
        $response = $this->getJson('api/posts/' . $post->slug);
        $response
            ->assertStatus(200)
            ->assertJson([
                'title' => $post->title,
                'slug' => $post->slug,
                'text' => $post->text,
                'author' => [
                    'name' => $author->name,
                    'email' => $author->email
                ]
            ]);
    }

    public function test_store_guest()
    {
        $post = [
            'title' => 'Guest post',
            'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec nec aliquam orci. Pellentesque auctor vehicula efficitur. Curabitur erat ligula, elementum vulputate elementum ut, sollicitudin sollicitudin ex. Aliquam erat volutpat. Donec volutpat eget neque id consequat. Nulla ultrices dui odio. Mauris turpis magna, finibus vel magna a, scelerisque convallis nulla. Morbi nec nisi quam. Fusce vitae consequat arcu. Nam eu iaculis massa. Nam sapien leo, pellentesque quis velit a, maximus laoreet felis.'
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
            'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec nec aliquam orci. Pellentesque auctor vehicula efficitur. Curabitur erat ligula, elementum vulputate elementum ut, sollicitudin sollicitudin ex. Aliquam erat volutpat. Donec volutpat eget neque id consequat. Nulla ultrices dui odio. Mauris turpis magna, finibus vel magna a, scelerisque convallis nulla. Morbi nec nisi quam. Fusce vitae consequat arcu. Nam eu iaculis massa. Nam sapien leo, pellentesque quis velit a, maximus laoreet felis.'
        ];
        Sanctum::actingAs(User::factory()->create());
        $request = $this->postJson('api/posts', $post);
        $request
            ->assertStatus(201)
            ->assertJson([
                'title' => $post['title'],
                'text' => $post['text']
            ]);
    }

    public function test_store_validation() {
        Sanctum::actingAs(User::factory()->create());
        $request = $this->postJson('api/posts', []);
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
        Sanctum::actingAs(User::factory()->create());
        $request = $this->putJson('api/posts/'.$post->slug, ['text' => 'test']);
        $request
            ->assertStatus(403)
            ->assertJson([
                'message' => 'This action is unauthorized.'
            ]);
    }

    public function test_update_author() {
        $post = Post::query()->first();
        $post->text = 'text';
        $user = User::query()->where('id', $post->user_id)->first();
        Sanctum::actingAs($user);
        $request = $this->putJson('api/posts/'.$post->slug, ['text' => 'text']);
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
            'role' => '1'
        ]);
        Sanctum::actingAs($user);
        $request = $this->putJson('api/posts/'.$post->slug, ['text' => 'test']);
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
            'role' => '1'
        ]);
        Sanctum::actingAs($user);
        $request = $this->putJson('api/posts/_', ['text' => 'test']);
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
        Sanctum::actingAs($user);
        $request = $this->deleteJson('api/posts/'.$post->slug);
        $request
            ->assertStatus(403)
            ->assertJson([
                'message' => 'This action is unauthorized.'
            ]);
    }

    public function test_destroy_author() {
        $post = Post::query()->first();
        $user = User::query()->where('id', $post->user_id)->first();
        Sanctum::actingAs($user);
        $request = $this->deleteJson('api/posts/'.$post->slug);
        $request
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Post removed successfully'
            ]);
    }

    public function test_destroy_moderator() {
        $post = Post::query()->first();
        $user = User::factory()->make([
            'role' => '1'
        ]);
        Sanctum::actingAs($user);
        $request = $this->deleteJson('api/posts/'.$post->slug);
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
        Sanctum::actingAs($user);
        $request = $this->deleteJson('api/posts/_');
        $request
            ->assertStatus(404)
            ->assertJson([
                'message' => 'Post not found'
            ]);
    }
}
