<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained()->onDelete('cascade');
            $table->integer('order_number')->default(1);
            $table->json('fields')->nullable(); // Dynamic field values defined by event admin
            $table->timestamps();

            $table->index('election_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
