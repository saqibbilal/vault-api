<?php

use App\Models\User;
use App\Models\Document;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated users cannot access documents', function () {
    $this->getJson('/api/v1/documents')
        ->assertStatus(401);
});

test('authenticated users can create a document', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user); // Simulates a logged-in user

    $response = $this->postJson('/api/v1/documents', [
        'title' => 'My Secret Note',
        'content' => 'This is the content of the note.',
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.title', 'My Secret Note');

    $this->assertDatabaseHas('documents', [
        'title' => 'My Secret Note',
        'user_id' => $user->id,
    ]);
});

test('authenticated users can list their documents', function () {
    $user = User::factory()->create();
    Document::factory()->count(3)->create(['user_id' => $user->id]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/documents');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

test('a user cannot view another users document', function () {
    // 1. Create two users
    $owner = User::factory()->create();
    $intruder = User::factory()->create();

    // 2. Create a document belonging to the owner
    $document = Document::factory()->create(['user_id' => $owner->id]);

    // 3. Log in as the intruder
    Sanctum::actingAs($intruder);

    // 4. Try to access the owner's document
    $response = $this->getJson("/api/v1/documents/{$document->id}");

    // 5. Expect a 403 Forbidden
    $response->assertStatus(403);
});

test('a user can view their own document', function () {
    $user = User::factory()->create();
    $document = Document::factory()->create(['user_id' => $user->id]);

    Sanctum::actingAs($user);

    $response = $this->getJson("/api/v1/documents/{$document->id}");

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $document->id);
});

test('super admins can view any document', function () {
    // 1. Seed the roles first so 'super-admin' exists in the test DB
    $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);

    // 2. Create a regular user and their document
    $owner = User::factory()->create();
    $document = Document::factory()->create(['user_id' => $owner->id]);

    // 3. Create a Super Admin
    $admin = User::factory()->create();
    $admin->assignRole('super-admin'); // Explicitly set the guard

    // 4. Log in as Admin
    Sanctum::actingAs($admin);

    // 5. Access the owner's document
    $response = $this->getJson("/api/v1/documents/{$document->id}");

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $document->id);
});



