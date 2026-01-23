<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\BuildingEffect;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Main Building (Base)
        $mainBuilding = Building::create([
            'name' => 'Main Building',
            'code' => 'main_building',
            'price' => 0,
            'description' => 'The foundation of your kingdom. Required to build other structures.',
            'is_unique' => true,
            'max_quantity' => 1,
        ]);

        // Wall (Defense)
        $wall = Building::create([
            'name' => 'Wall',
            'code' => 'wall',
            'price' => 300,
            'description' => 'Increases defense by 10',
            'is_unique' => false,
            'max_quantity' => null,
        ]);
        BuildingEffect::create([
            'building_id' => $wall->id,
            'key' => 'defense_bonus',
            'value' => '10',
            'data_type' => 'integer',
            'description' => 'Defense point increase',
        ]);

        // Gold Mine Level 1
        $goldMine1 = Building::create([
            'name' => 'Gold Mine Lvl 1',
            'code' => 'gold_mine_1',
            'price' => 100,
            'description' => 'Increases gold production by 2 per interval',
            'is_unique' => false,
            'max_quantity' => null,
        ]);
        BuildingEffect::create([
            'building_id' => $goldMine1->id,
            'key' => 'gold_production',
            'value' => '2',
            'data_type' => 'integer',
            'description' => 'Gold production increase',
        ]);

        // Gold Mine Level 2
        $goldMine2 = Building::create([
            'name' => 'Gold Mine Lvl 2',
            'code' => 'gold_mine_2',
            'price' => 500,
            'description' => 'Increases gold production by 5 per interval',
            'is_unique' => false,
            'max_quantity' => null,
        ]);
        BuildingEffect::create([
            'building_id' => $goldMine2->id,
            'key' => 'gold_production_lvl2',
            'value' => '5',
            'data_type' => 'integer',
            'description' => 'Gold production increase',
        ]);

        // Gold Mine Level 3
        $goldMine3 = Building::create([
            'name' => 'Gold Mine Lvl 3',
            'code' => 'gold_mine_3',
            'price' => 1000,
            'description' => 'Increases gold production by 10 per interval',
            'is_unique' => false,
            'max_quantity' => null,
        ]);
        BuildingEffect::create([
            'building_id' => $goldMine3->id,
            'key' => 'gold_production_lvl3',
            'value' => '10',
            'data_type' => 'integer',
            'description' => 'Gold production increase',
        ]);

        // Barrack Level 1
        $barrack1 = Building::create([
            'name' => 'Barrack Lvl 1',
            'code' => 'barrack_1',
            'price' => 200,
            'description' => 'Adds 5 troops production per minute',
            'is_unique' => false,
            'max_quantity' => null,
        ]);
        BuildingEffect::create([
            'building_id' => $barrack1->id,
            'key' => 'troops_production',
            'value' => '5',
            'data_type' => 'integer',
            'description' => 'Troops production per minute',
        ]);

        // Barrack Level 2
        $barrack2 = Building::create([
            'name' => 'Barrack Lvl 2',
            'code' => 'barrack_2',
            'price' => 500,
            'description' => 'Adds 10 troops production per minute',
            'is_unique' => false,
            'max_quantity' => null,
        ]);
        BuildingEffect::create([
            'building_id' => $barrack2->id,
            'key' => 'troops_production_lvl2',
            'value' => '10',
            'data_type' => 'integer',
            'description' => 'Troops production per minute',
        ]);

        // Barrack Level 3
        $barrack3 = Building::create([
            'name' => 'Barrack Lvl 3',
            'code' => 'barrack_3',
            'price' => 1000,
            'description' => 'Adds 20 troops production per minute',
            'is_unique' => false,
            'max_quantity' => null,
        ]);
        BuildingEffect::create([
            'building_id' => $barrack3->id,
            'key' => 'troops_production_lvl3',
            'value' => '20',
            'data_type' => 'integer',
            'description' => 'Troops production per minute',
        ]);
    }
}
