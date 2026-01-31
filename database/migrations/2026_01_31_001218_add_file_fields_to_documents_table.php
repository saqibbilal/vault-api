<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Making content nullable since it's optional for file uploads
            $table->text('content')->nullable()->change();

            // Adding the new metadata fields
            $table->string('file_path')->nullable()->after('content');
            $table->string('mime_type')->nullable()->after('file_path');
            $table->unsignedBigInteger('size')->default(0)->after('mime_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->text('content')->nullable(false)->change();
            $table->dropColumn(['file_path', 'mime_type', 'size']);
        });
    }
};
