# Quick Reference Guide - Character Appearance System

## 📋 What Was Implemented

### 1. **Dashboard Character Display**
   - Location: [resources/views/dashboard.blade.php](resources/views/dashboard.blade.php#L39-L78)
   - Shows character preview with tribe-specific background
   - Ready for future character part layering

### 2. **Database Tables**
   - `character_parts` - stores all character parts (head, body, arm, leg)
   - `users` - added columns: head_id, body_id, arm_id, leg_id

### 3. **Default Character Parts** (16 total)
   - Marksman: 4 parts (head, body, arm, leg)
   - Tank: 4 parts
   - Mage: 4 parts
   - Warrior: 4 parts

### 4. **Asset Folders Created**
   ```
   public/storage/assets/
   ├── backgrounds/        (4 tribe backgrounds)
   ├── marksman/          (4 character parts)
   ├── tank/              (4 character parts)
   ├── mage/              (4 character parts)
   └── warrior/           (4 character parts)
   ```

### 5. **Placeholder Images**
   - All 20 placeholder images generated ✅
   - Ready to be replaced with your actual artwork

## 🚀 How to Replace Placeholders

### Option 1: Simple Replacement
1. Create your PNG images
2. Name them exactly as the placeholders
3. Replace files in `public/storage/assets/`

### Option 2: Add New Variants
1. Create new images
2. Add database records via seeder or admin panel
3. Update user's part_id when they select new variant

## 📝 Adding Assets Checklist

### For Backgrounds (1920x1080 recommended):
- [ ] Create Marksman.png
- [ ] Create Tank.png
- [ ] Create Mage.png
- [ ] Create Warrior.png
- [ ] Place in `public/storage/assets/backgrounds/`

### For Character Parts (256x256 transparent PNG):
Each tribe needs:
- [ ] head_default.png
- [ ] body_default.png
- [ ] arm_default.png
- [ ] leg_default.png

## 💡 Quick Commands

```bash
# Run migrations
php artisan migrate

# Seed character parts
php artisan db:seed --class=CharacterPartSeeder

# Generate placeholder images
php create_placeholders.php

# View preview
# Open character_system_preview.html in browser
```

## 🔍 Database Queries Examples

```php
// Get user's character parts
$user = Auth::user();
$head = $user->head;        // CharacterPart model
$body = $user->body;
$arm = $user->arm;
$leg = $user->leg;

// Get all parts for a tribe
$tribe = Tribe::find(1);
$parts = $tribe->characterParts;

// Get default parts for a tribe
$defaults = CharacterPart::where('tribe_id', 1)
    ->where('is_default', true)
    ->get();

// Change user's head
$newHead = CharacterPart::where('tribe_id', $user->tribe_id)
    ->where('part_type', 'head')
    ->where('id', '!=', $user->head_id)
    ->first();
$user->update(['head_id' => $newHead->id]);
```

## 📁 Key Files Reference

| File | Purpose |
|------|---------|
| `CHARACTER_APPEARANCE.md` | Full system documentation |
| `IMPLEMENTATION_SUMMARY.md` | Complete implementation details |
| `PLACEHOLDER_GUIDE.md` | How to create/replace assets |
| `create_placeholders.php` | Script to generate placeholders |
| `character_system_preview.html` | Visual preview of system |

## 🎨 Asset Specifications

### Character Parts:
- **Format**: PNG with transparency
- **Size**: 256x256px minimum
- **Background**: Transparent
- **Naming**: `{part_type}_default.png` or `{part_type}_{variant}.png`

### Backgrounds:
- **Format**: PNG or JPG
- **Size**: 1920x1080px (Full HD)
- **Naming**: Exact tribe name (e.g., `Marksman.png`)

## ✨ Future Enhancements Ideas

1. **Character Customization Page**
   - Allow users to preview and change parts
   - Show unlocked vs locked parts
   - Purchase system for premium parts

2. **Character Display Enhancement**
   - Layer parts with CSS/Canvas
   - Add animations
   - Implement equipment slots

3. **Marketplace**
   - Trade parts between users
   - Auction system
   - Limited edition parts

## 🐛 Troubleshooting

### Images not showing?
- Check file paths match database records
- Verify storage link: `php artisan storage:link`
- Check file permissions

### Parts not assigned on registration?
- Ensure CharacterPartSeeder has run
- Check tribe_id is set correctly
- Verify foreign keys exist

### Need to reset?
```bash
php artisan migrate:fresh --seed
php artisan db:seed --class=CharacterPartSeeder
```

## 📞 Need Help?

Review these files in order:
1. `IMPLEMENTATION_SUMMARY.md` - What was done
2. `CHARACTER_APPEARANCE.md` - How system works
3. `PLACEHOLDER_GUIDE.md` - How to add assets

---

**Status**: ✅ System Complete & Ready
**Next Step**: Replace placeholders with your artwork!
