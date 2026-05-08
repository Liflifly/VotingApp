<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_invite_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('token')->unique(); // Raw token stored (use hash in lookup)
            $table->string('role')->default('admin'); // admin | super_admin
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable(); // null = still valid
            $table->foreignId('used_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['event_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_invite_tokens');
    }
};
