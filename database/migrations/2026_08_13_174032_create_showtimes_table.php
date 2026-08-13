<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('showtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('theater_id')->constrained()->cascadeOnDelete();
            $table->foreignId('film_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('time', 16);
            $table->string('tech')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('currency', 8)->nullable();
            $table->text('booking_url')->nullable();
            $table->timestamps();

            $table->index(['theater_id', 'date']);
            $table->index(['film_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('showtimes');
    }
};
