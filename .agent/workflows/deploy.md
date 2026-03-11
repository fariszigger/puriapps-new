---
description: How to deploy puriapps-new to the hosting server (puriappsv2.bprpuri.com)
---

# Deployment Workflow for PuriApps

## Hosting Structure

- **Project directory (server):** `~/puriapps-new/` — contains the full Laravel codebase
- **Web root (public-facing):** `~/puriappsv2.bprpuri.com/` — this is where the web server serves files from
- **Build assets** must be copied from `~/puriapps-new/public/build/` to `~/puriappsv2.bprpuri.com/build/`

> [!IMPORTANT]
> The web root (`puriappsv2.bprpuri.com`) is separate from the project directory (`puriapps-new`).
> After pulling new code or rebuilding assets, you MUST copy `public/build/` to the web root.

## Deployment Steps

SSH into the server, then:

### 1. Pull latest code
```bash
cd ~/puriapps-new
git pull origin main
```

### 2. Install dependencies (if composer.json changed)
```bash
composer install --no-dev --optimize-autoloader
```

### 3. Run migrations (if any new migrations)
```bash
php artisan migrate --force
```

### 4. Copy build assets to web root
```bash
cp -r ~/puriapps-new/public/build/* ~/puriappsv2.bprpuri.com/build/
```

### 5. Clear caches
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Notes

- **Permissions (roles)** are stored in the database, not in code. If a new permission is added locally (e.g. via Manajemen Role UI), it must also be added on the hosting database manually or via `php artisan tinker`.
- The `RolePermissionSeeder.php` is currently empty — permissions are managed through the UI.
- `.env` is gitignored. The hosting `.env` has `APP_URL=http://puriappsv2.bprpuri.com`.
- Compiled Vite assets (`public/build/`) are committed to the repo, so no need to run `npm run build` on the server.
