#!/usr/bin/env bash
#
# Deploys/updates the Magang BAKTI codebase into place: clones (first run) or
# pulls, installs PHP + Node dependencies, builds frontend assets, runs
# migrations, fixes permissions, and (re)caches Laravel config.
#
# Assumes deploy/01-provision-server.sh has already been run and the MySQL
# database + user already exist (see deploy/README.md).
#
# Usage:
#   sudo bash deploy/02-deploy-app.sh <repo-url> [target-dir]
#
# Example:
#   sudo bash deploy/02-deploy-app.sh git@github.com:org/webbakti.git /var/www/webbakti
#
# On subsequent deploys, run it from inside an already-cloned repo without
# the repo-url argument to just pull + rebuild:
#   sudo bash /var/www/webbakti/deploy/02-deploy-app.sh --update

set -euo pipefail

APP_DIR="${2:-/var/www/webbakti}"
REPO_URL="${1:-}"
WEB_USER="www-data"

if [[ $EUID -ne 0 ]]; then
  echo "Run this as root (sudo bash deploy/02-deploy-app.sh ...)." >&2
  exit 1
fi

# Earlier runs chown the whole app dir to www-data, which makes Git refuse
# to operate on it as any other user ("dubious ownership"). Whitelist it
# globally so both root and the deploying user can run git here.
git config --global --add safe.directory "$APP_DIR" 2>/dev/null || true

if [[ "$REPO_URL" == "--update" ]]; then
  APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
  git config --global --add safe.directory "$APP_DIR" 2>/dev/null || true
  echo "==> Updating existing deployment at $APP_DIR"
  cd "$APP_DIR"
  git pull
elif [[ -n "$REPO_URL" ]]; then
  if [[ -d "$APP_DIR/.git" ]]; then
    echo "==> $APP_DIR already exists, pulling latest instead of cloning"
    cd "$APP_DIR"
    git pull
  else
    echo "==> Cloning $REPO_URL into $APP_DIR"
    mkdir -p "$(dirname "$APP_DIR")"
    git clone "$REPO_URL" "$APP_DIR"
    cd "$APP_DIR"
  fi
else
  echo "Usage: sudo bash deploy/02-deploy-app.sh <repo-url> [target-dir]" >&2
  echo "   or: sudo bash deploy/02-deploy-app.sh --update   (run from inside an existing clone)" >&2
  exit 1
fi

cd "$APP_DIR"

echo "==> Installing PHP dependencies (composer install --no-dev)"
composer install --optimize-autoloader --no-dev --no-interaction

if [[ ! -f .env ]]; then
  echo "==> No .env found, copying .env.example"
  cp .env.example .env
  php artisan key:generate
  echo ""
  echo "!! .env was just created from .env.example with placeholder values."
  echo "!! Edit $APP_DIR/.env now (DB_*, MAIL_*, APP_URL) before continuing,"
  echo "!! then re-run this script with --update to pick up the new config."
  exit 0
fi

echo "==> Installing Node dependencies and building frontend assets"
echo "    (node_modules is kept after build: PDF rendering and face verification"
echo "    call 'node' as a runtime child process from PHP, not just at build time)"
npm install
npm run build

echo "==> Running database migrations"
php artisan migrate --force

echo "==> Creating storage symlink (idempotent)"
php artisan storage:link || true

echo "==> Fixing ownership and permissions"
chown -R "$WEB_USER":"$WEB_USER" "$APP_DIR"
# Exclude node_modules/.bin and vendor/bin: chmod 644 on every file would
# strip the executable bit from Node/Composer binaries (e.g. node_modules/.bin/vite),
# breaking `npm run build` on every subsequent deploy with "Permission denied".
find "$APP_DIR" -type f -not -path "*/node_modules/.bin/*" -not -path "*/vendor/bin/*" -exec chmod 644 {} \;
find "$APP_DIR" -type d -exec chmod 755 {} \;
chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"

echo "==> Verifying www-data can execute node (required for PDF/face verification)"
if ! sudo -u "$WEB_USER" node -v >/dev/null 2>&1; then
  echo "!! WARNING: www-data cannot run 'node'. PDF generation and face verification will fail at runtime." >&2
  echo "!! Make sure Node was installed globally via NodeSource (deploy/01-provision-server.sh), not via nvm for a specific user." >&2
fi

echo "==> Caching Laravel config/routes/views for production"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo ""
echo "==> Deployment finished."
echo ""
echo "Remaining manual steps (see deploy/README.md for details):"
echo "  - Configure the Apache virtual host (deploy/webbakti.apache.conf)"
echo "  - Set up the Supervisor queue worker (deploy/webbakti-worker.conf)"
echo "  - Add the Laravel scheduler cron entry"
echo "  - Optionally seed demo data: php artisan db:seed --class=AdminSeeder"
echo "    (AdminSeeder creates admin@bakti.com with password '1' — CHANGE IT IMMEDIATELY if you seed it)"
