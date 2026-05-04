<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'nis' => 'SA0001',
            'has_voted' => false,
            'role' => 'super_admin',
        ]);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'nis' => 'A0001',
            'has_voted' => false,
            'role' => 'admin',
        ]);

        $students = [
            ['name' => 'Siswa 1', 'email' => 'siswa1@example.com', 'nis' => 'S1001'],
            ['name' => 'Siswa 2', 'email' => 'siswa2@example.com', 'nis' => 'S1002'],
            ['name' => 'Siswa 3', 'email' => 'siswa3@example.com', 'nis' => 'S1003'],
            ['name' => 'Siswa 4', 'email' => 'siswa4@example.com', 'nis' => 'S1004'],
        ];

        foreach ($students as $s) {
            User::create([
                'name' => $s['name'],
                'email' => $s['email'],
                'password' => Hash::make('password'),
                'nis' => $s['nis'],
                'has_voted' => false,
                'role' => 'user',
            ]);
        }

        $candidates = [
            ['name' => 'Kandidat A', 'order_number' => 1, 'class' => 'XI RPL 1', 'vision' => 'Visi A', 'mission' => 'Misi A'],
            ['name' => 'Kandidat B', 'order_number' => 2, 'class' => 'XI RPL 2', 'vision' => 'Visi B', 'mission' => 'Misi B'],
            ['name' => 'Kandidat C', 'order_number' => 3, 'class' => 'XI RPL 3', 'vision' => 'Visi C', 'mission' => 'Misi C'],
            ['name' => 'Kandidat D', 'order_number' => 4, 'class' => 'XI RPL 4', 'vision' => 'Visi D', 'mission' => 'Misi D'],
        ];

        $createdCandidates = [];
        foreach ($candidates as $c) {
            $createdCandidates[] = Candidate::create($c);
        }

        $election = Election::create([
            'name' => 'Pemilihan Ketua Kosgoro 2026',
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addHours(5),
            'status' => 'active',
        ]);

        $u1 = User::where('email', 'siswa1@example.com')->first();
        $u2 = User::where('email', 'siswa2@example.com')->first();
        $u3 = User::where('email', 'siswa3@example.com')->first();

        if ($u1 && isset($createdCandidates[0])) {
            Vote::create([
                'user_id' => $u1->id,
                'candidate_id' => $createdCandidates[0]->id,
                'election_id' => $election->id,
            ]);
            $u1->update([
                'has_voted' => true,
                'voted_election_id' => $election->id,
            ]);
        }
        if ($u2 && isset($createdCandidates[1])) {
            Vote::create([
                'user_id' => $u2->id,
                'candidate_id' => $createdCandidates[1]->id,
                'election_id' => $election->id,
            ]);
            $u2->update([
                'has_voted' => true,
                'voted_election_id' => $election->id,
            ]);
        }
        if ($u3 && isset($createdCandidates[0])) {
            Vote::create([
                'user_id' => $u3->id,
                'candidate_id' => $createdCandidates[0]->id,
                'election_id' => $election->id,
            ]);
            $u3->update([
                'has_voted' => true,
                'voted_election_id' => $election->id,
            ]);
        }
    }
}
