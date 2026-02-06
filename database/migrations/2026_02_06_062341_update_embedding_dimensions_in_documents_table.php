<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Truncate to ensure no dimension mismatches during the change
        DB::table('documents')->truncate();

        Schema::table('documents', function (Blueprint $table) {
            // We use change() to modify the existing column.
            // Note: You must have 'doctrine/dbal' installed for change() to work in older Laravel,
            // but Laravel 11+ handles this natively.
            $table->vector('embedding', 3072)->change();
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->vector('embedding', 768)->change();
        });
    }
};
