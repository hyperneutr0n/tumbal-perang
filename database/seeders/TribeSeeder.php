<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tribe;
use App\Models\StatType;
use App\Models\TribeStat;

class TribeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Stat Types first
        $statTypes = [
            ['code' => 'magic_attack', 'name' => 'Magic Attack', 'category' => 'attack', 'attack_type' => 'magic'],
            ['code' => 'range_attack', 'name' => 'Range Attack', 'category' => 'attack', 'attack_type' => 'range'],
            ['code' => 'melee_attack', 'name' => 'Melee Attack', 'category' => 'attack', 'attack_type' => 'melee'],
            ['code' => 'magic_defense', 'name' => 'Magic Defense', 'category' => 'defense', 'attack_type' => 'magic'],
            ['code' => 'range_defense', 'name' => 'Range Defense', 'category' => 'defense', 'attack_type' => 'range'],
            ['code' => 'melee_defense', 'name' => 'Melee Defense', 'category' => 'defense', 'attack_type' => 'melee'],
        ];

        foreach ($statTypes as $statType) {
            StatType::firstOrCreate(['code' => $statType['code']], $statType);
        }

        // Create Tribes
        $tribes = [
            [
                'name' => 'Marksman',
                'description' => 'Suku yang mempunyai kelebihan di kekuatan serangan jarak jauh, namun kekurangan di pertahanan.',
                'troops_per_minute' => 5,
                'stats' => [
                    'magic_attack' => 30,
                    'range_attack' => 100,  // Very strong
                    'melee_attack' => 40,
                    'magic_defense' => 30,
                    'range_defense' => 40,
                    'melee_defense' => 30,  // Weak
                ]
            ],
            [
                'name' => 'Tank',
                'description' => 'Suku yang mempunyai kelebihan di kekuatan pertahanan, namun sangat lemah di kekuatan serang.',
                'troops_per_minute' => 5,
                'stats' => [
                    'magic_attack' => 20,   // Very weak
                    'range_attack' => 20,   // Very weak
                    'melee_attack' => 20,   // Very weak
                    'magic_defense' => 100, // Strong
                    'range_defense' => 100, // Strong
                    'melee_defense' => 100, // Strong
                ]
            ],
            [
                'name' => 'Mage',
                'description' => 'Suku yang sangat kuat di serangan magic, namun sangat lemah di pertahanan.',
                'troops_per_minute' => 5,
                'stats' => [
                    'magic_attack' => 100,  // Very strong
                    'range_attack' => 40,
                    'melee_attack' => 40,
                    'magic_defense' => 20,  // Very weak
                    'range_defense' => 20,  // Very weak
                    'melee_defense' => 20,  // Very weak
                ]
            ],
            [
                'name' => 'Warrior',
                'description' => 'Suku yang sangat kuat di serangan jarak dekat, cukup kuat di pertahanan, namun sangat lemah di pertahanan terhadap magic dan serangan jarak jauh.',
                'troops_per_minute' => 5,
                'stats' => [
                    'magic_attack' => 40,
                    'range_attack' => 40,
                    'melee_attack' => 100,  // Very strong
                    'magic_defense' => 20,  // Very weak
                    'range_defense' => 20,  // Very weak
                    'melee_defense' => 60,  // Decent
                ]
            ],
        ];

        foreach ($tribes as $tribeData) {
            $stats = $tribeData['stats'];
            unset($tribeData['stats']);
            
            $tribe = Tribe::firstOrCreate(['name' => $tribeData['name']], $tribeData);
            
            // Create tribe stats
            foreach ($stats as $statCode => $value) {
                $statType = StatType::where('code', $statCode)->first();
                if ($statType) {
                    TribeStat::updateOrCreate(
                        [
                            'tribe_id' => $tribe->id,
                            'stat_type_id' => $statType->id,
                        ],
                        [
                            'value' => $value
                        ]
                    );
                }
            }
        }
    }
}
