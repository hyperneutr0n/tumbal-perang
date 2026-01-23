<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\CharacterPart;

echo "Updating user character parts...\n\n";

$users = User::all();

foreach ($users as $user) {
    if (!$user->tribe_id) {
        echo "User {$user->username} has no tribe, skipping...\n";
        continue;
    }

    // Get default parts for the user's tribe
    $headPart = CharacterPart::where('tribe_id', $user->tribe_id)
        ->where('part_type', 'head')
        ->where('is_default', true)
        ->first();
    
    $bodyPart = CharacterPart::where('tribe_id', $user->tribe_id)
        ->where('part_type', 'body')
        ->where('is_default', true)
        ->first();
    
    $armPart = CharacterPart::where('tribe_id', $user->tribe_id)
        ->where('part_type', 'arm')
        ->where('is_default', true)
        ->first();
    
    $legPart = CharacterPart::where('tribe_id', $user->tribe_id)
        ->where('part_type', 'leg')
        ->where('is_default', true)
        ->first();

    if ($headPart && $bodyPart && $armPart && $legPart) {
        $user->update([
            'head_id' => $headPart->id,
            'body_id' => $bodyPart->id,
            'arm_id' => $armPart->id,
            'leg_id' => $legPart->id,
        ]);
        echo "Updated user: {$user->username} with tribe {$user->tribe->name}\n";
    } else {
        echo "WARNING: Could not find all parts for user {$user->username}\n";
    }
}

echo "\nDone! Updated " . $users->count() . " users.\n";
