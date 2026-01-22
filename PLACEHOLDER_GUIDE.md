# Creating Placeholder Images

Since the assets are still ongoing, here's how you can create placeholder images:

## Option 1: Using Online Tools

### For Character Parts (PNG with transparency):
1. Visit: https://placeholder.com/
2. Use dimensions: 256x256
3. Add text like "Marksman Head", "Tank Body", etc.
4. Download as PNG
5. Remove background using: https://www.remove.bg/

### For Backgrounds:
1. Visit: https://placeholder.com/
2. Use dimensions: 1920x1080
3. Add tribe name as text
4. Download and place in backgrounds folder

## Option 2: Using ImageMagick (Command Line)

If you have ImageMagick installed, run these commands:

```bash
# Create character part placeholders (with transparency)
convert -size 256x256 xc:transparent -pointsize 24 -fill black -gravity center -annotate +0+0 "Marksman\nHead" public/storage/assets/marksman/head_default.png

# Create background placeholders
convert -size 1920x1080 gradient:#4A5568-#2D3748 -pointsize 72 -fill white -gravity center -annotate +0+0 "Marksman" public/storage/assets/backgrounds/Marksman.png
```

## Option 3: Using PHP GD (Script Below)

Run this script to auto-generate placeholders:

```php
<?php
// Save this as create_placeholders.php in the project root and run: php create_placeholders.php

$tribes = ['Marksman', 'Tank', 'Mage', 'Warrior'];
$parts = ['head', 'body', 'arm', 'leg'];
$colors = [
    'Marksman' => [100, 150, 255], // Blue
    'Tank' => [150, 150, 150],      // Gray
    'Mage' => [200, 100, 255],      // Purple
    'Warrior' => [255, 100, 100],   // Red
];

// Create backgrounds
foreach ($tribes as $tribe) {
    $img = imagecreatetruecolor(800, 600);
    $color = $colors[$tribe];
    $bgColor = imagecolorallocate($img, $color[0], $color[1], $color[2]);
    $textColor = imagecolorallocate($img, 255, 255, 255);
    
    imagefill($img, 0, 0, $bgColor);
    $text = $tribe . " Background";
    imagestring($img, 5, 300, 290, $text, $textColor);
    
    $path = "public/storage/assets/backgrounds/{$tribe}.png";
    imagepng($img, $path);
    imagedestroy($img);
    echo "Created: {$path}\n";
}

// Create character parts
foreach ($tribes as $tribe) {
    $tribeLower = strtolower($tribe);
    
    foreach ($parts as $part) {
        $img = imagecreatetruecolor(256, 256);
        
        // Make background transparent
        imagesavealpha($img, true);
        $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefill($img, 0, 0, $transparent);
        
        $color = $colors[$tribe];
        $partColor = imagecolorallocate($img, $color[0], $color[1], $color[2]);
        $textColor = imagecolorallocate($img, 255, 255, 255);
        
        // Draw a simple shape
        imagefilledrectangle($img, 50, 50, 206, 206, $partColor);
        
        // Add text
        $text = ucfirst($part);
        imagestring($img, 3, 100, 120, $text, $textColor);
        
        $path = "public/storage/assets/{$tribeLower}/{$part}_default.png";
        imagepng($img, $path);
        imagedestroy($img);
        echo "Created: {$path}\n";
    }
}

echo "\nAll placeholder images created successfully!\n";
```

## Quick Test Files

For quick testing, you can also just create simple colored squares:
- Use any image editor
- Create 256x256 images with different colors for each part
- Create 800x600 images for backgrounds
- Save with the correct naming convention

## Recommended Asset Specifications

### Character Parts:
- **Format**: PNG with transparency
- **Size**: 256x256 pixels minimum
- **Layers**: Each part should be on a transparent background
- **Alignment**: Center the part within the canvas
- **Style**: Consistent art style across all parts

### Backgrounds:
- **Format**: PNG or JPG
- **Size**: 1920x1080 pixels (Full HD)
- **Theme**: Match the tribe's characteristics
  - Marksman: Forest/Nature theme
  - Tank: Stone/Fortress theme
  - Mage: Mystical/Magical theme
  - Warrior: Battle/Arena theme
