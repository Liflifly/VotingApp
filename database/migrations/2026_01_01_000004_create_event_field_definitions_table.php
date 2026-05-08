<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_field_definitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('target');            // 'voter' | 'candidate'
            $table->string('key');               // Programmatic key (e.g., 'department')
            $table->string('label');             // Display label (e.g., 'Department')
            $table->string('type');              // text | textarea | number | email | select | image
            $table->json('options')->nullable(); // For 'select' type: array of choices
            $table->boolean('required')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->unique(['event_id', 'target', 'key']);
            $table->index(['event_id', 'target']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_field_definitions');
    }
};
