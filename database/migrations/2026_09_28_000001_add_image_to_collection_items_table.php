<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Guarded so the migration stays re-runnable when the column was
        // already added from a SQL import (see the coordinates migration).
        if (! Schema::hasColumn('collection_items', 'image')) {
            Schema::table('collection_items', function (Blueprint $table) {
                $table->string('image')->nullable()->after('icon');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('collection_items', 'image')) {
            Schema::table('collection_items', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }
};
