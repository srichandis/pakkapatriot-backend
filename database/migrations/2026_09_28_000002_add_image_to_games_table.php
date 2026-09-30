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
        if (! Schema::hasColumn('games', 'image')) {
            Schema::table('games', function (Blueprint $table) {
                $table->string('image')->nullable()->after('badge');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('games', 'image')) {
            Schema::table('games', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }
};
