#!/usr/bin/env bash
# bundle-online.sh — JALAN ONLINE SEKALI di Linux. Hasil: apt-cache/*.deb + cbt-akm-offline-bundle.tar.gz
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
OUT="$ROOT/deploy/offline"
CACHE="$OUT/apt-cache"
BUNDLE="$OUT/cbt-akm-offline-bundle.tar.gz"
cd "$ROOT"
command -v php >/dev/null || { echo "butuh php"; exit 1; }
command -v npm >/dev/null || { echo "butuh npm"; exit 1; }
if command -v composer >/dev/null; then COMPOSER=composer; elif [ -f /tmp/composer ]; then COMPOSER="php /tmp/composer"; else echo "butuh composer (atau /tmp/composer)"; exit 1; fi
$COMPOSER install --no-dev --prefer-dist --no-interaction --optimize-autoloader
npm ci
npm run build
rm -rf "$CACHE" && mkdir -p "$CACHE"
PKGS="nginx php8.3-fpm php8.3-opcache php8.3-cli php8.3-mysql php8.3-sqlite3 php8.3-xml php8.3-mbstring php8.3-curl php8.3-zip php8.3-intl php8.3-bcmath mysql-server sqlite3"
sudo apt-get update
DEPS=$(apt-cache depends --recurse --no-recommends --no-suggests --no-conflicts --no-breaks --no-replaces --no-enhances $PKGS | grep -oP '^\s*Depends: \K[^<>]+' | tr -d ' ' | sort -u | tr '\n' ' ')
(cd "$CACHE" && apt-get download $DEPS)
echo "deb: $(ls "$CACHE"/*.deb | wc -l) file"
rm -f "$BUNDLE"
tar -czf "$BUNDLE" vendor public/build composer.json composer.lock package.json package-lock.json .env.example artisan app bootstrap config database resources routes storage public/index.php
echo "bundle: $BUNDLE"
tar -tzf "$BUNDLE" | head -5
echo OK
