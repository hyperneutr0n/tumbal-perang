<?php
/**
 * Placeholder Image Generator
 * Run this script to create placeholder images for character parts and backgrounds
 * Usage: php create_placeholders.php
 */

$tribes = ['Marksman', 'Tank', 'Mage', 'Warrior'];
$parts = ['head', 'body', 'arm', 'leg'];
$colors = [
    'Marksman' => [100, 150, 255], // Blue
    'Tank' => [150, 150, 150],      // Gray
    'Mage' => [200, 100, 255],      // Purple
    'Warrior' => [255, 100, 100],   // Red
];

echo "Creating placeholder images...\n\n";

// Create backgrounds
echo "Creating background images:\n";
foreach ($tribes as $tribe) {
    $img = imagecreatetruecolor(800, 600);
    $color = $colors[$tribe];
    $bgColor = imagecolorallocate($img, $color[0], $color[1], $color[2]);
    $textColor = imagecolorallocate($img, 255, 255, 255);
    
    imagefill($img, 0, 0, $bgColor);
    
    // Add tribe name
    $font = 5; // Built-in font
    $text = $tribe . " Background";
    $textWidth = imagefontwidth($font) * strlen($text);
    $textHeight = imagefontheight($font);
    $x = (800 - $textWidth) / 2;
    $y = (600 - $textHeight) / 2;
    
    imagestring($img, $font, $x, $y, $text, $textColor);
    
    $path = "public/storage/assets/backgrounds/{$tribe}.png";
    
    // Create directory if it doesn't exist
    if (!is_dir(dirname($path))) {
        mkdir(dirname($path), 0755, true);
    }
    
    imagepng($img, $path);
    imagedestroy($img);
    echo "  ✓ Created: {$path}\n";
}

// Create character parts
echo "\nCreating character part images:\n";
foreach ($tribes as $tribe) {
    $tribeLower = strtolower($tribe);
    
    echo "  {$tribe}:\n";
    
    foreach ($parts as $part) {
        $img = imagecreatetruecolor(256, 256);
        
        // Make background transparent
        imagesavealpha($img, true);
        $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefill($img, 0, 0, $transparent);
        
        $color = $colors[$tribe];
        $partColor = imagecolorallocate($img, $color[0], $color[1], $color[2]);
        $textColor = imagecolorallocate($img, 255, 255, 255);
        $borderColor = imagecolorallocate($img, 0, 0, 0);
        
        // Draw a simple shape with border
        imagefilledrectangle($img, 50, 50, 206, 206, $partColor);
        imagerectangle($img, 50, 50, 206, 206, $borderColor);
        
        // Add part name
        $font = 3;
        $text = ucfirst($part);
        $textWidth = imagefontwidth($font) * strlen($text);
        $textHeight = imagefontheight($font);
        $x = (256 - $textWidth) / 2;
        $y = (256 - $textHeight) / 2;
        
        imagestring($img, $font, $x, $y, $text, $textColor);
        
        $path = "public/storage/assets/{$tribeLower}/{$part}_default.png";
        
        // Create directory if it doesn't exist
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        
        imagepng($img, $path);
        imagedestroy($img);
        echo "    ✓ {$part}_default.png\n";
    }
}

echo "\n✅ All placeholder images created successfully!\n";
echo "\nYou can now replace these with your actual artwork.\n";
echo "Placeholders are located in: public/storage/assets/\n";
