# Basic Laravel 12 Installation

This is a clean, minimal Laravel 12 installation with Vite for asset bundling.

## Features

- ✅ Laravel Framework 12.39.0
- ✅ Vite for asset bundling
- ✅ No UI frameworks (no Tailwind, no Bootstrap, no jQuery)
- ✅ SQLite database (default)
- ✅ Basic authentication scaffolding
- ✅ Default Laravel migrations only

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js & NPM

## Installation

1. Install PHP dependencies:
```bash
composer install
```

2. Install NPM dependencies:
```bash
npm install
```

3. Create environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Create database:
```bash
touch database/database.sqlite
```

6. Run migrations:
```bash
php artisan migrate
```

7. Build assets:
```bash
npm run build
```

## Development

### Start the development server:
```bash
php artisan serve
```

The application will be available at http://127.0.0.1:8000

### Watch for asset changes:
```bash
npm run dev
```

## File Structure

- `app/` - Application code
- `resources/` - Views, CSS, and JavaScript files
- `routes/` - Application routes
- `database/` - Migrations and seeders
- `public/` - Public assets and entry point

## Custom Files Backup

Custom migrations and old configurations have been moved to `_____backup/` directory for reference.

## License

This Laravel application is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
