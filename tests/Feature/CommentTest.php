<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\CommentSeeder;
use Database\Seeders\PostSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CommentTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
        $this->seed(UserSeeder::class);
        $this->seed(PostSeeder::class);
        $this->seed(CommentSeeder::class);
    }

    public function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    public function test_index_not_exist_post() {
        $request = $this->getJson('/api/posts/_/comments');
        $request
            ->assertStatus(404)
            ->assertJson([
                'message' => 'No query results for model [App\\Models\\Post] _'
            ]);
    }

    public function test_index_exist_post() {
        $post = Post::query()->whereHas('comments')->first();
        $request = $this->getJson('/api/posts/'.$post->slug.'/comments');
        $request
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'author' => [
                        'name',
                        'email'
                    ],
                    'text',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function test_show_not_exist_comment() {
        $post = Post::factory()->create();
        $request = $this->getJson('/api/posts/'.$post->slug.'/comments/0');
        $request
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Comment not found'
            ]);
    }

    public function test_show_exist_comment() {
        $comment = Comment::query()->with(['post', 'user'])->first();
        $request = $this->getJson('/api/posts/'.$comment->post->slug.'/comments/'.$comment->id);
        $request
            ->assertStatus(200)
            ->assertJson([
                'author' => [
                    'name' => $comment->user->name,
                    'email' => $comment->user->email
                ],
                'text' => $comment->text
            ]);
    }

    public function test_store_guest()
    {
        $post = Post::factory()->create();
        $request = $this->postJson('api/posts/'.$post->slug.'/comments', ['text' => 'test']);
        $request
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_store_user()
    {
        $post = Post::factory()->create();
        $request = $this
            ->actingAs(User::factory()->create(), 'sanctum')
            ->postJson('api/posts/'.$post->slug.'/comments', ['text' => 'test']);
        $request
            ->assertStatus(201)
            ->assertJson([
                'text' => 'test'
            ]);
    }

    public function test_store_validation() {
        $post = Post::factory()->create();
        $request = $this
            ->actingAs(User::factory()->create(), 'sanctum')
            ->postJson('api/posts/'.$post->slug.'/comments', []);
        $request
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'The text field is required.'
                ]
            ]);
    }

    public function test_update_guest() {
        $comment = Comment::query()->with('post')->first();
        $request = $this->putJson('api/posts/'.$comment->post->slug.'/comments/'.$comment->id, ['text' => 'test']);
        $request
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_update_not_author() {
        $comment = Comment::query()->with('post')->first();
        $request = $this
            ->actingAs(User::factory()->create(), 'sanctum')
            ->putJson('api/posts/'.$comment->post->slug.'/comments/'.$comment->id, ['text' => 'test']);
        $request
            ->assertStatus(403)
            ->assertJson([
                'message' => 'This action is unauthorized.'
            ]);
    }

    public function test_update_author() {
        $comment = Comment::query()->with(['post', 'user'])->first();
        $request = $this
            ->actingAs($comment->user, 'sanctum')
            ->putJson('api/posts/'.$comment->post->slug.'/comments/'.$comment->id, ['text' => 'test']);
        $request
            ->assertStatus(200)
            ->assertJson([
                'text' => 'test',
            ]);
    }

    public function test_update_moderator() {
        $comment = Comment::query()->with('post')->first();
        $user = User::factory()->make([
            'role' => Role::MODERATOR
        ]);
        $request = $this
            ->actingAs($user, 'sanctum')
            ->putJson('api/posts/'.$comment->post->slug.'/comments/'.$comment->id, ['text' => 'test']);
        $request
            ->assertStatus(200)
            ->assertJson([
                'text' => 'test',
            ]);
    }

    public function test_update_not_exist_comment() {
        $user = User::factory()->make([
            'role' => Role::MODERATOR
        ]);
        $post = Post::query()->first();
        $request = $this
            ->actingAs($user, 'sanctum')
            ->putJson('api/posts/'.$post->slug.'/comments/0', ['text' => 'test']);
        $request
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Comment not found'
            ]);
    }

    public function test_destroy_guest() {
        $comment = Comment::query()->with('post')->first();
        $request = $this->deleteJson('api/posts/'.$comment->post->slug.'/comments/'.$comment->id);
        $request
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_destroy_not_author() {
        $comment = Comment::query()->with('post')->first();
        $request = $this
            ->actingAs(User::factory()->make(), 'sanctum')
            ->deleteJson('api/posts/'.$comment->post->slug.'/comments/'.$comment->id);
        $request
            ->assertStatus(403)
            ->assertJson([
                'message' => 'This action is unauthorized.'
            ]);
    }

    public function test_destroy_author() {
        $comment = Comment::query()->with(['post', 'user'])->first();
        $request = $this
            ->actingAs($comment->user, 'sanctum')
            ->deleteJson('api/posts/'.$comment->post->slug.'/comments/'.$comment->id);
        $request
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Comment removed successfully'
            ]);
    }

    public function test_destroy_moderator() {
        $comment = Comment::query()->with('post')->first();
        $user = User::factory()->make([
            'role' => Role::MODERATOR
        ]);
        $request = $this
            ->actingAs($user, 'sanctum')
            ->deleteJson('api/posts/'.$comment->post->slug.'/comments/'.$comment->id);
        $request
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Comment removed successfully'
            ]);
    }

    public function test_destroy_not_exist_comment() {
        $post = Post::query()->first();
        $user = User::factory()->make([
            'role' => Role::MODERATOR
        ]);
        $request = $this
            ->actingAs($user, 'sanctum')
            ->deleteJson('api/posts/'.$post->slug.'/comments/0');
        $request
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Comment not found'
            ]);
    }
}
