<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('voter_access_token', 64)->nullable()->unique()->after('status');
            $table->string('admin_access_token', 64)->nullable()->unique()->after('voter_access_token');
            $table->enum('results_visibility', ['public', 'private'])->default('private')->after('admin_access_token');
        });

        // Generate tokens for existing events
        foreach (\App\Models\Event::all() as $event) {
            $event->voter_access_token = Str::random(32);
            $event->admin_access_token = Str::random(32);
            $event->save();
        }
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['voter_access_token', 'admin_access_token', 'results_visibility']);
        });
    }
};
