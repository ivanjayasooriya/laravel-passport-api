<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    // Generate Passport client for testing database instances
    Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
});

test('any authenticated user can list posts', function () {
    /** @var TestCase $this */

    $user = User::factory()->create();
    Post::factory(3)->create();

    Passport::actingAs($user);

    $response = $this->getJson('/api/posts');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'title', 'content', 'user', 'created_at'],
            ],
        ]);
});

test('authenticated user can create a post', function () {
    /** @var TestCase $this */

    $user = User::factory()->create();
    Passport::actingAs($user);

    $response = $this->postJson('/api/posts', [
        'title' => 'My First Post',
        'content' => 'This is the content of the test post.',
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Post created successfully',
            'data' => [
                'title' => 'My First Post',
                'content' => 'This is the content of the test post.',
            ],
        ]);

    $this->assertDatabaseHas('posts', ['title' => 'My First Post', 'user_id' => $user->id]);
});

test('user can update their own post', function () {
    /** @var TestCase $this */

    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    Passport::actingAs($user);

    $response = $this->putJson("/api/posts/{$post->id}", [
        'title' => 'Updated Title',
        'content' => 'Updated Content',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Post updated successfully',
            'data' => [
                'title' => 'Updated Title',
            ],
        ]);
});

test('user cannot update another users post', function () {
    /** @var TestCase $this */

    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $owner->id]);

    Passport::actingAs($otherUser);

    $response = $this->putJson("/api/posts/{$post->id}", [
        'title' => 'Hacked Title',
        'content' => 'Hacked Content',
    ]);

    $response->assertStatus(403);
});

test('admin can update any users post via policy override', function () {
    /** @var TestCase $this */

    $owner = User::factory()->create();
    $admin = User::factory()->admin()->create();
    $post = Post::factory()->create(['user_id' => $owner->id]);

    Passport::actingAs($admin);

    $response = $this->putJson("/api/posts/{$post->id}", [
        'title' => 'Admin Updated Title',
        'content' => 'Admin Updated Content',
    ]);

    $response->assertStatus(200);
});

test('user can delete their own post', function () {
    /** @var TestCase $this */

    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    Passport::actingAs($user);

    $response = $this->deleteJson("/api/posts/{$post->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Post deleted successfully',
        ]);

    $this->assertDatabaseMissing('posts', ['id' => $post->id]);
});

test('post creation fails with missing title or content', function () {
    /** @var TestCase $this */

    $user = User::factory()->create();
    Passport::actingAs($user);

    $response = $this->postJson('/api/posts', [
        'title' => '',
        'content' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'content']);
});

test('returns 404 when showing a non-existent post', function () {
    /** @var TestCase $this */

    $user = User::factory()->create();
    Passport::actingAs($user);

    $response = $this->getJson('/api/posts/99999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Post not found',
        ]);
});

test('returns 404 when updating a non-existent post', function () {
    /** @var TestCase $this */

    $user = User::factory()->create();
    Passport::actingAs($user);

    $response = $this->putJson('/api/posts/99999', [
        'title' => 'Updated Title',
        'content' => 'Updated Content',
    ]);

    $response->assertStatus(404);
});

test('returns 404 when deleting a non-existent post', function () {
    /** @var TestCase $this */
    
    $user = User::factory()->create();
    Passport::actingAs($user);

    $response = $this->deleteJson('/api/posts/99999');

    $response->assertStatus(404);
});