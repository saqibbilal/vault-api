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
        $user = User::first() ?? User::factory()->create();

        // This "silences" the Observer for this block only
        Document::withoutEvents(function () use ($user) {
            Document::factory()->count(10)->create([
                'user_id' => $user->id,
                // We manually provide a fake vector so the DB doesn't complain
                'embedding' => array_map(fn() => rand(-100, 100) / 100, range(1, 768))
            ]);
        });
    }
}
