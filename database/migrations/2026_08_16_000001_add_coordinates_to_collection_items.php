<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The coordinates columns may already exist when the table was
        // created from a SQL import (e.g. admin-collections-data.sql), so
        // add them only when missing to keep the migration re-runnable.
        if (! Schema::hasColumn('collection_items', 'latitude')) {
            Schema::table('collection_items', function (Blueprint $table) {
                $table->decimal('latitude', 10, 7)->nullable()->after('region');
            });
        }

        if (! Schema::hasColumn('collection_items', 'longitude')) {
            Schema::table('collection_items', function (Blueprint $table) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            });
        }
    }

    public function down(): void
    {
        Schema::table('collection_items', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
