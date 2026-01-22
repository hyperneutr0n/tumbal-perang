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
        $tribes = Tribe::all();

        foreach ($tribes as $tribe) {
            // Create default character parts for each tribe
            $parts = [
                [
                    'part_type' => 'head',
                    'name' => $tribe->name . ' Head',
                    'image_path' => 'assets/' . strtolower($tribe->name) . '/head_default.png',
                    'is_default' => true,
                ],
                [
                    'part_type' => 'body',
                    'name' => $tribe->name . ' Body',
                    'image_path' => 'assets/' . strtolower($tribe->name) . '/body_default.png',
                    'is_default' => true,
                ],
                [
                    'part_type' => 'arm',
                    'name' => $tribe->name . ' Arm',
                    'image_path' => 'assets/' . strtolower($tribe->name) . '/arm_default.png',
                    'is_default' => true,
                ],
                [
                    'part_type' => 'leg',
                    'name' => $tribe->name . ' Leg',
                    'image_path' => 'assets/' . strtolower($tribe->name) . '/leg_default.png',
                    'is_default' => true,
                ],
            ];

            foreach ($parts as $part) {
                CharacterPart::firstOrCreate(
                    [
                        'tribe_id' => $tribe->id,
                        'part_type' => $part['part_type'],
                        'is_default' => true,
                    ],
                    array_merge($part, ['tribe_id' => $tribe->id])
                );
            }
        }
    }
}
