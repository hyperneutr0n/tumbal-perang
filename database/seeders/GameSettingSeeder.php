<?php

namespace Database\Seeders;

use App\Models\GameSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        GameSetting::create([
            'key' => 'default_gold_per_minute',
            'value' => '5',
            'data_type' => 'integer'
        ]);
    }
}
