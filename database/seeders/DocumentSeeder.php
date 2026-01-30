<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Document;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the test user we likely created in DatabaseSeeder or create a new one
        $user = User::first() ?? User::factory()->contributor()->create([
            'name' => 'Test Contributor',
            'email' => 'contributor@vault.test',
        ]);

        Document::factory()
            ->count(10)
            ->create(['user_id' => $user->id]);
    }
}
