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
