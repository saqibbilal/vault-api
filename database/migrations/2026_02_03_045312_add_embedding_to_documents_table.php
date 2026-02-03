<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // We ensure the extension exists first
        DB::statement('CREATE EXTENSION IF NOT EXISTS vector');

        Schema::table('documents', function (Blueprint $table) {
            // vector(768) is the dimension for Gemini text-embedding-004
            DB::statement('ALTER TABLE documents ADD COLUMN embedding vector(768) NULL');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('embedding');
        });
    }
};
