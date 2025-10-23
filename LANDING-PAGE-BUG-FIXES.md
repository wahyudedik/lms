# Landing Page Bug Fixes

## Issues Fixed

### 1. `count(): Argument #1 ($value) must be of type Countable|array, string given`
**Location**: `resources/views/welcome.blade.php:129`

**Cause**: The `features` and `statistics` fields were returning JSON strings instead of arrays.

### 2. `foreach() argument must be of type array|object, string given`
**Location**: `resources/views/admin/landing-page/edit.blade.php:260`

**Cause**: Same issue - `features` and `statistics` were JSON strings instead of arrays.

---

## Root Cause

The `School` model had **BOTH**:
1. Cast definitions: `'features' => 'array'`
2. Custom accessor methods: `getFeaturesAttribute()`, `getStatisticsAttribute()`

This created a **conflict** where:
- Laravel would try to cast the JSON string to an array
- Then pass the result to the accessor
- The accessor would try to `json_decode()` an already-decoded value
- Result: inconsistent data types (sometimes string, sometimes array)

---

## Solution

### Step 1: Remove Conflicting Casts
Removed `features` and `statistics` from the `$casts` array in `app/Models/School.php`:

```php
protected $casts = [
    'is_active' => 'boolean',
    'show_landing_page' => 'boolean',
    // Removed 'features' and 'statistics' - using custom accessors instead
];
```

### Step 2: Update Accessor Methods
Simplified the accessor methods to only handle JSON strings:

```php
public function getFeaturesAttribute($value)
{
    // Decode JSON string to array
    $features = !empty($value) ? json_decode($value, true) : [];

    // Return default features if empty or invalid JSON
    if (empty($features) || !is_array($features)) {
        return [/* default features */];
    }

    return $features;
}
```

### Step 3: Add Mutator Methods
Added mutators to automatically encode arrays to JSON when saving:

```php
public function setFeaturesAttribute($value)
{
    $this->attributes['features'] = is_array($value) ? json_encode($value) : $value;
}

public function setStatisticsAttribute($value)
{
    $this->attributes['statistics'] = is_array($value) ? json_encode($value) : $value;
}
```

---

## How It Works Now

### Reading Data
```php
$school->features;  // Accessor decodes JSON → returns array
```

### Writing Data
```php
$school->features = ['icon' => '...', 'title' => '...'];  // Mutator encodes to JSON → saves string
```

### In Blade Templates
```blade
@if ($school->features && count($school->features) > 0)
    @foreach ($school->features as $feature)
        {{ $feature['title'] }}
    @endforeach
@endif
```

---

## Files Modified

1. `app/Models/School.php`
   - Removed `features` and `statistics` from `$casts`
   - Updated `getFeaturesAttribute()` and `getStatisticsAttribute()`
   - Added `setFeaturesAttribute()` and `setStatisticsAttribute()`

2. Cleared all caches with `php artisan optimize:clear`

---

## Testing

### Test Landing Page
1. Visit `http://lms.test/`
2. Should display without errors
3. Features and statistics should render correctly

### Test Landing Page Editor
1. Login as Admin
2. Go to **Administrator** → **Schools**
3. Click 🖌️ icon for a school
4. Should display edit form without errors
5. Features and statistics tabs should work correctly

---

## Prevention

**Best Practice**: Don't use both casts and accessors/mutators for the same attribute.

Choose one approach:
- **Option A**: Use casts only (simple cases)
- **Option B**: Use accessors/mutators only (complex cases with defaults/transformations)

In our case, we chose **Option B** because we need custom default values when fields are empty.

---

## Related Files

- `resources/views/welcome.blade.php` - Landing page view
- `resources/views/admin/landing-page/edit.blade.php` - Landing page editor
- `app/Http/Controllers/Admin/LandingPageController.php` - Landing page controller
- `database/migrations/2025_10_23_211517_add_landing_page_fields_to_schools_table.php` - Migration

---

**Date**: 2025-10-23  
**Status**: ✅ Fixed and Tested

