# Laravel Debugbar Setup for Cursor

Laravel Debugbar has been installed and configured for use with Cursor IDE.

## Installation Status

✅ **Installed**: `barryvdh/laravel-debugbar` (v3.16)
✅ **Configured**: Editor set to `cursor`
✅ **Enabled**: Automatically enabled when `APP_DEBUG=true`

## Configuration

### Current Settings

The Debugbar is configured in `config/debugbar.php`:

- **Editor**: `cursor` (configured for Cursor IDE)
- **Enabled**: Automatically when `APP_DEBUG=true` in `.env`
- **Storage**: File-based storage in `storage/debugbar/`
- **Theme**: Auto (respects system preferences)

### Environment Variables

Add these to your `.env` file if you want to override defaults:

```env
# Enable/disable Debugbar (null = auto, based on APP_DEBUG)
DEBUGBAR_ENABLED=true

# Editor for file links (already set to 'cursor')
DEBUGBAR_EDITOR=cursor

# Theme: auto, light, or dark
DEBUGBAR_THEME=auto

# Open storage for viewing previous requests (development only!)
# WARNING: Only enable in local development, not in production!
DEBUGBAR_OPEN_STORAGE=true

# Path mapping for remote development (if using Docker/Vagrant)
# DEBUGBAR_REMOTE_SITES_PATH=/var/www/html
# DEBUGBAR_LOCAL_SITES_PATH=/Users/g470/Sites/inlando_all/seperated_admin_laravel/admin-laravel
```

## Features Enabled

The following collectors are enabled by default:

- ✅ **Messages** - Log messages and dumps
- ✅ **Time** - Request timing
- ✅ **Memory** - Memory usage
- ✅ **Exceptions** - Exception displayer
- ✅ **Database** - SQL queries with bindings
- ✅ **Views** - Rendered views
- ✅ **Route** - Current route information
- ✅ **Auth** - Authentication status
- ✅ **Gate** - Authorization checks
- ✅ **Mail** - Email messages
- ✅ **Laravel** - Version and environment
- ✅ **Files** - Included files
- ✅ **Models** - Eloquent models
- ✅ **Livewire** - Livewire components (when available)

## Usage

### Viewing Debugbar

1. **In Browser**: The debugbar appears at the bottom of your page when:
   - `APP_DEBUG=true` in `.env`
   - You're in local environment
   - The request is not excluded (see `except` array in config)

2. **Opening Files in Cursor**: 
   - Click on any file path in the debugbar
   - It will open in Cursor IDE automatically (configured via `editor` setting)

3. **Viewing Previous Requests**:
   - Visit `/_debugbar/open` to view stored requests
   - Only works if `DEBUGBAR_OPEN_STORAGE=true` (local development only!)

### Database Queries

- View all SQL queries executed during the request
- See query bindings and execution time
- Copy queries for testing
- View backtraces to see where queries originated

### Performance Monitoring

- **Time Tab**: See how long each part of your request took
- **Memory Tab**: Monitor memory usage
- **Timeline**: Visual representation of request lifecycle

### Excluded Routes

The following routes are excluded from Debugbar:
- `telescope/*`
- `horizon/*`

Add more exclusions in `config/debugbar.php`:

```php
'except' => [
    'telescope*',
    'horizon*',
    'api/*',  // Example: exclude API routes
],
```

## Cursor Integration

### File Links

When you click on file paths in the Debugbar:
- Files open directly in Cursor IDE
- Line numbers are preserved
- Works with both local and remote development

### Path Mapping (for Docker/Remote)

If you're using Docker or a remote development server, configure path mapping:

```env
DEBUGBAR_REMOTE_SITES_PATH=/var/www/html
DEBUGBAR_LOCAL_SITES_PATH=/Users/g470/Sites/inlando_all/seperated_admin_laravel/admin-laravel
```

This allows file links to work correctly when files are in different locations.

## Troubleshooting

### Debugbar Not Showing

1. **Check APP_DEBUG**:
   ```bash
   grep APP_DEBUG .env
   # Should be: APP_DEBUG=true
   ```

2. **Clear Config Cache**:
   ```bash
   php artisan config:clear
   ```

3. **Check if Route is Excluded**:
   - Check `except` array in `config/debugbar.php`
   - Your current route might be excluded

4. **Check Storage Directory**:
   ```bash
   ls -la storage/debugbar/
   # Directory should exist and be writable
   ```

### File Links Not Opening in Cursor

1. **Verify Editor Setting**:
   ```bash
   grep DEBUGBAR_EDITOR .env
   # Or check config/debugbar.php line 65
   ```

2. **Check Cursor Protocol**:
   - Cursor should be registered to handle `cursor://` protocol
   - Restart Cursor if links don't work

3. **Path Mapping**:
   - If using Docker/remote, set path mapping in `.env`

## Security Notes

⚠️ **Important**: 
- Debugbar should **NEVER** be enabled in production
- `DEBUGBAR_OPEN_STORAGE` should only be `true` in local development
- The debugbar exposes sensitive information (queries, config, etc.)

## Additional Resources

- [Laravel Debugbar Documentation](https://github.com/barryvdh/laravel-debugbar)
- [Debugbar Configuration Options](https://github.com/barryvdh/laravel-debugbar#configuration)

## Quick Commands

```bash
# Clear config cache (after changing .env)
php artisan config:clear

# View debugbar routes
php artisan route:list | grep debugbar

# Check if debugbar is enabled
php artisan tinker
>>> config('debugbar.enabled')
```

---

**Status**: ✅ Ready to use! Debugbar will appear automatically when `APP_DEBUG=true`.

