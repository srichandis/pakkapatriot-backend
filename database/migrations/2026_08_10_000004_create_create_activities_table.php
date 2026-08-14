<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('create_activities', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('badge')->nullable();
            $table->string('title');
            $table->string('emoji')->nullable();
            $table->string('tagline')->nullable();
            $table->text('what_is')->nullable();
            $table->json('known_for')->nullable();
            $table->json('try_this')->nullable();
            $table->json('related')->nullable();
            $table->string('hero_accent')->nullable();
            $table->string('tile')->nullable();
            $table->string('button')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('create_activities');
    }
};
