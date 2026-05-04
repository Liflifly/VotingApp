<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('elections', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->enum('status', ['draft', 'active', 'ended'])->default('draft')->after('name');
            $table->integer('total_voters')->default(0)->after('ends_at');
            $table->text('notes')->nullable()->after('total_voters');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('elections', function (Blueprint $table) {
            $table->dropColumn(['name', 'status', 'total_voters', 'notes']);
        });
    }
};
