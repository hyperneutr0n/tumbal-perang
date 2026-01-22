# Character Appearance System

## Overview
The character appearance system allows users to have customizable character visuals based on their tribe selection. Each user has character parts (head, body, arm, leg) that are displayed on the dashboard.

## Database Structure

### Character Parts Table
- **id**: Primary key
- **tribe_id**: Foreign key to tribes table
- **part_type**: Enum ('head', 'body', 'arm', 'leg')
- **name**: Name of the part
- **image_path**: Path to the image file
- **is_default**: Boolean indicating if this is the default part for the tribe

### Users Table (New Columns)
- **head_id**: Foreign key to character_parts table
- **body_id**: Foreign key to character_parts table
- **arm_id**: Foreign key to character_parts table
- **leg_id**: Foreign key to character_parts table

## Features Implemented

### 1. Dashboard Character Display
- Location: `resources/views/dashboard.blade.php`
- Shows a character preview area with tribe-specific background
- Displays character parts placeholder
- Background image path: `/storage/assets/backgrounds/{TribeName}.png`

### 2. Character Parts Model
- Location: `app/Models/CharacterPart.php`
- Relationships:
  - `belongsTo(Tribe::class)` - Each part belongs to a tribe

### 3. User Model Updates
- Location: `app/Models/User.php`
- New relationships added:
  - `head()` - BelongsTo CharacterPart
  - `body()` - BelongsTo CharacterPart
  - `arm()` - BelongsTo CharacterPart
  - `leg()` - BelongsTo CharacterPart

### 4. Tribe Model Updates
- Location: `app/Models/Tribe.php`
- New relationship:
  - `characterParts()` - HasMany CharacterPart

### 5. Character Registration Flow
- Location: `app/Http/Controllers/CharacterController.php`
- When a user selects a tribe during registration:
  1. Default character parts for that tribe are retrieved
  2. User is assigned the default head, body, arm, and leg
  3. User can later customize these parts (future feature)

### 6. Asset Folder Structure
- Location: `public/storage/assets/`
- Organized by tribe:
  ```
  assets/
  ├── backgrounds/
  │   └── {TribeName}.png
  ├── marksman/
  │   ├── head_default.png
  │   ├── body_default.png
  │   ├── arm_default.png
  │   └── leg_default.png
  ├── tank/
  ├── mage/
  └── warrior/
  ```

## Default Character Parts

Each tribe has 4 default parts seeded automatically:
- **Marksman**: Head, Body, Arm, Leg
- **Tank**: Head, Body, Arm, Leg
- **Mage**: Head, Body, Arm, Leg
- **Warrior**: Head, Body, Arm, Leg

## How to Add Assets

### Background Images
1. Create a PNG/JPG image (recommended: 1920x1080)
2. Save as `{TribeName}.png` (e.g., `Marksman.png`)
3. Place in `public/storage/assets/backgrounds/`

### Character Parts
1. Create PNG images with transparent backgrounds (recommended: 256x256)
2. Name according to convention: `{part_type}_{variant}.png`
3. Place in the appropriate tribe folder
4. For new variants (not defaults), add database records

## Future Enhancements

- [ ] Add character customization page
- [ ] Allow users to unlock/purchase new character parts
- [ ] Implement character part layering on dashboard
- [ ] Add animation support for character parts
- [ ] Create admin panel for managing character parts
- [ ] Add rarity levels for character parts
- [ ] Implement character part marketplace

## Usage in Code

### Getting User's Character Parts
```php
$user = Auth::user();
$head = $user->head; // Returns CharacterPart model
$body = $user->body;
$arm = $user->arm;
$leg = $user->leg;
```

### Getting All Parts for a Tribe
```php
$tribe = Tribe::find(1);
$allParts = $tribe->characterParts;
$heads = $tribe->characterParts()->where('part_type', 'head')->get();
```

### Displaying Character in Blade
```blade
@if(auth()->user()->head)
    <img src="/storage/{{ auth()->user()->head->image_path }}" alt="Head">
@endif
```
