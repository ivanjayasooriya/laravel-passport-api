<?php

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

test('user can register successfully', function () {
    /** @var TestCase $this */

    $response = $this->postJson('/api/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user' => ['id', 'name', 'email', 'role'],
                'token',
            ],
        ]);

    $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
});

test('user can login with valid credentials', function () {
    /** @var TestCase $this */

    $user = User::factory()->create([
        'email' => 'jane@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'jane@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user',
                'token',
            ],
        ]);
});

test('authenticated user can view their profile', function () {
    /** @var TestCase $this */

    $user = User::factory()->create();
    Passport::actingAs($user);

    $response = $this->getJson('/api/profile');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
            ],
        ]);
});

test('regular user cannot access admin user list', function () {
    /** @var TestCase $this */

    $user = User::factory()->create(['role' => 'user']);
    Passport::actingAs($user);

    $response = $this->getJson('/api/admin/users');

    $response->assertStatus(403);
});

test('admin user can access admin user list', function () {
    /** @var TestCase $this */
    
    $admin = User::factory()->admin()->create();
    Passport::actingAs($admin);

    $response = $this->getJson('/api/admin/users');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);
});