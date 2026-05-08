<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('voter'); // voter | admin | super_admin
            $table->json('metadata')->nullable();      // Dynamic voter data fields
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
            $table->index(['event_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_user');
    }
};
