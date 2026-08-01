#!/bin/bash
set -Eeuo pipefail

REPOSITORY_ROOT="/home/kisanworld/repositories/Kassan-world"
LIVE_ROOT="/home/kisanworld/public_html"
BACKUP_ROOT="/home/kisanworld/deploy-backups"
STATE_FILE="/home/kisanworld/.kassan-last-deployed"
LOCK_FILE="/home/kisanworld/.kassan-deploy.lock"
PHP_BIN="/usr/local/bin/php"
COMPOSER_BIN="/home/kisanworld/bin/composer"
NODE_BIN_DIR="/opt/alt/alt-nodejs22/root/usr/bin"

exec 9>"$LOCK_FILE"
/bin/flock -n 9 || { echo "Another deployment is already running."; exit 0; }

cd "$REPOSITORY_ROOT"

test -f artisan
test -f composer.json
test -f package-lock.json
test -f "$LIVE_ROOT/.env"
test -d "$LIVE_ROOT/storage"
test -d "$LIVE_ROOT/storage/app/public/uploads"
test -x "$PHP_BIN"
test -f "$COMPOSER_BIN"
test -x "$NODE_BIN_DIR/npm"

export PATH="$NODE_BIN_DIR:/usr/local/bin:/usr/bin:/bin"
export COMPOSER_HOME="/home/kisanworld/.composer"

echo "Installing production PHP dependencies..."
"$PHP_BIN" "$COMPOSER_BIN" install \
  --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

echo "Building frontend assets..."
"$NODE_BIN_DIR/npm" ci --no-audit --no-fund
"$NODE_BIN_DIR/npm" run build

test -f vendor/autoload.php
test -f public/build/manifest.json

if [[ "${DRY_RUN:-0}" == "1" ]]; then
  echo "DRY RUN passed. Live files were not changed."
  exit 0
fi

DEPLOY_SHA="$(git rev-parse HEAD)"
BACKUP_DIR="$BACKUP_ROOT/$(date -u +%Y%m%dT%H%M%SZ)-${DEPLOY_SHA:0:12}"
/bin/mkdir -p "$BACKUP_DIR"

echo "Creating a rollback backup of application code..."
/bin/tar \
  --exclude='./storage' \
  --exclude='./vendor' \
  --exclude='./node_modules' \
  --exclude='./public/build' \
  --exclude='./bootstrap/cache/*.php' \
  -C "$LIVE_ROOT" -cf - . | /bin/tar -C "$BACKUP_DIR" -xf -

MAINTENANCE_STARTED=0
finish() {
  if [[ "$MAINTENANCE_STARTED" == "1" ]]; then
    cd "$LIVE_ROOT"
    "$PHP_BIN" artisan up || true
  fi
}
trap finish EXIT

cd "$LIVE_ROOT"
"$PHP_BIN" artisan down --retry=30 || true
MAINTENANCE_STARTED=1

echo "Copying application code while preserving live data..."
cd "$REPOSITORY_ROOT"
/bin/tar \
  --exclude='./.git' \
  --exclude='./.github' \
  --exclude='./.cpanel.yml' \
  --exclude='./.agents' \
  --exclude='./scripts' \
  --exclude='./tests' \
  --exclude='./.env' \
  --exclude='./.env.*' \
  --exclude='./storage' \
  --exclude='./node_modules' \
  --exclude='./public/storage' \
  --exclude='./public/hot' \
  --exclude='./bootstrap/cache/*.php' \
  -cf - . | /bin/tar --no-overwrite-dir -C "$LIVE_ROOT" -xf -

cd "$LIVE_ROOT"
"$PHP_BIN" artisan package:discover --ansi
"$PHP_BIN" artisan optimize:clear
"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan view:cache

/bin/printf '%s\n' "$DEPLOY_SHA" > "$STATE_FILE"
echo "Deployment completed: $DEPLOY_SHA"
