<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tribe;
use App\Models\CharacterPart;

class CharacterPartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CharacterPart::truncate();

        $tribes = Tribe::all();

        foreach ($tribes as $tribe) {
            $tribeLower = strtolower($tribe->name);
        
            $parts = [
                [
                    'part_type' => 'head',
                    'name' => $tribe->name . ' Head',
                    'image_path' => 'assets/' . $tribeLower . '/kepala.png',
                    'is_default' => true,
                ],
                [
                    'part_type' => 'body',
                    'name' => $tribe->name . ' Body',
                    'image_path' => 'assets/' . $tribeLower . '/badan.png',
                    'is_default' => true,
                ],
                [
                    'part_type' => 'arm',
                    'name' => $tribe->name . ' Arms',
                    'image_path' => 'assets/' . $tribeLower . '/tangan.png',
                    'is_default' => true,
                ],
                [
                    'part_type' => 'leg',
                    'name' => $tribe->name . ' Legs',
                    'image_path' => 'assets/' . $tribeLower . '/kaki.png',
                    'is_default' => true,
                ],
            ];

            foreach ($parts as $part) {
                CharacterPart::create(array_merge($part, ['tribe_id' => $tribe->id]));
            }
        }
    }
}
