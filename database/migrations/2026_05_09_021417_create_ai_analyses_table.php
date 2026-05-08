<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('election_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('analysis_type', ['results_summary', 'recommendation']);
            $table->string('prompt_hash', 64)->index(); // SHA-256 hash for deduplication
            $table->longText('response_text');
            $table->string('model_used', 100)->default('llama3');
            $table->timestamps();

            $table->index(['event_id', 'analysis_type']);
            $table->index(['election_id', 'prompt_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_analyses');
    }
};
