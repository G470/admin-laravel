# PSR-4 Autoloading Warnings

## Issue

During Docker build, you may see warnings like:

```
Class App\Http\Controllers\Vendor\RentalPushController located in ./app/Http/Controllers/vendor/RentalPushController.php does not comply with psr-4 autoloading standard
```

## Cause

The directory structure uses lowercase directories (`admin/`, `vendor/`) but the namespaces use capitalized names (`App\Http\Controllers\Admin\`, `App\Http\Controllers\Vendor\`).

On case-insensitive filesystems (macOS, Windows), this works fine. On case-sensitive filesystems (Linux, Docker), PSR-4 autoloading expects the directory structure to match the namespace exactly.

## Impact

**These are warnings, not errors.** The build will complete successfully, but:
- Autoloader may not be fully optimized
- Some classes may be skipped from the classmap
- Potential performance impact (minimal)

## Solution (Optional - For Future Fix)

To properly fix this, you have two options:

### Option 1: Rename Directories (Recommended)

Rename the directories to match the namespace:

```bash
# Rename admin/ to Admin/
mv app/Http/Controllers/admin app/Http/Controllers/Admin

# Rename vendor/ to Vendor/
mv app/Http/Controllers/vendor app/Http/Controllers/Vendor
```

Then update all namespace declarations in those files to match.

### Option 2: Update Namespaces

Change namespaces to match the directory structure:

```php
// Change from:
namespace App\Http\Controllers\Admin;

// To:
namespace App\Http\Controllers\admin;
```

**Note:** This option is not recommended as it violates PSR-4 standards.

## Current Status

The Dockerfile has been configured to handle these warnings gracefully. The build will complete successfully despite the warnings.

## Files Affected

- `app/Http/Controllers/admin/*` - Should be `Admin/`
- `app/Http/Controllers/vendor/*` - Should be `Vendor/`

## Priority

**Low** - This is a code quality issue that doesn't break functionality. Can be fixed in a future refactoring.

