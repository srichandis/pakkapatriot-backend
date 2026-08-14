<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_items', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('slug');
            $table->string('name');
            $table->string('native_name')->nullable();
            $table->string('tagline')->nullable();
            $table->string('category')->nullable();
            $table->string('era')->nullable();
            $table->string('attribution')->nullable();
            $table->string('region')->nullable();
            $table->string('icon')->nullable();
            $table->string('accent')->nullable();
            $table->string('soft_accent')->nullable();
            $table->string('icon_color')->nullable();
            $table->string('quote')->nullable();
            $table->string('quote_source')->nullable();
            $table->text('summary')->nullable();
            $table->json('overview')->nullable();
            $table->json('core_ideas')->nullable();
            $table->text('legacy')->nullable();
            $table->timestamps();

            $table->unique(['type', 'slug']);
            $table->index('type');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_items');
    }
};
