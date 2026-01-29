<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a user can register and receives a contributor role', function () {

    // Clear cache inside the test to be safe
    $this->artisan('permission:cache-reset');

    // Before we test, we need the roles to exist in our test database
    $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);

    $response = $this->postJson('/api/v1/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(201);
});
