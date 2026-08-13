<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('external_id')->unique();
            $table->string('title');
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('poster')->nullable();
            $table->string('url')->nullable();
            $table->decimal('rating', 4, 2)->nullable();
            $table->unsignedInteger('cinema_count')->nullable();
            $table->unsignedInteger('session_count')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
