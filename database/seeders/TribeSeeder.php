<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tribe;

class TribeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tribes = [
            [
                'name' => 'Suku Merah',
                'description' => 'Suku Merah adalah suku yang terkenal dengan keberanian dan kekuatan dalam pertempuran.',
                'troops_per_minute' => 5
            ],
            [
                'name' => 'Suku Biru',
                'description' => 'Suku Biru adalah suku yang ahli dalam strategi dan diplomasi.',
                'troops_per_minute' => 5
            ],
            [
                'name' => 'Suku Hijau',
                'description' => 'Suku Hijau adalah suku yang berhubungan dengan alam dan memiliki sumber daya melimpah.',
                'troops_per_minute' => 5
            ],
            [
                'name' => 'Suku Kuning',
                'description' => 'Suku Kuning adalah suku yang kaya dan memiliki perdagangan yang kuat.',
                'troops_per_minute' => 5
            ],
        ];

        foreach ($tribes as $tribe) {
            Tribe::create($tribe);
        }
    }
}
