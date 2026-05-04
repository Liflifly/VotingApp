<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * DB-01 FIX: Buat kolom 'nis' menjadi nullable agar akun Admin dan Super Admin
 * bisa dibuat tanpa NIS (karena hanya Siswa yang memiliki NIS).
 *
 * DB-03 FIX: Tambahkan index pada kolom-kolom yang sering digunakan dalam query:
 *  - elections.status           (dipakai oleh scopeActive, history filter)
 *  - candidates.(election_id, order_number) compound index
 *  - votes.(election_id, candidate_id)      compound index untuk aggregasi hasil
 */
return new class extends Migration
{
    public function up(): void
    {
        // DB-01: NIS menjadi nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('nis')->nullable()->change();
        });

        // DB-03: Index pada elections.status
        Schema::table('elections', function (Blueprint $table) {
            $table->index('status', 'elections_status_index');
        });

        // DB-03: Compound index pada candidates (election_id, order_number)
        Schema::table('candidates', function (Blueprint $table) {
            $table->index(['election_id', 'order_number'], 'candidates_election_order_index');
        });

        // DB-03: Compound index pada votes (election_id, candidate_id)
        Schema::table('votes', function (Blueprint $table) {
            $table->index(['election_id', 'candidate_id'], 'votes_election_candidate_index');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nis')->nullable(false)->change();
        });

        Schema::table('elections', function (Blueprint $table) {
            $table->dropIndex('elections_status_index');
        });

        Schema::table('candidates', function (Blueprint $table) {
            $table->dropIndex('candidates_election_order_index');
        });

        Schema::table('votes', function (Blueprint $table) {
            $table->dropIndex('votes_election_candidate_index');
        });
    }
};
