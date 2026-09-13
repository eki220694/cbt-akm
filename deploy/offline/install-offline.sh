#!/usr/bin/env bash
# install-offline.sh — JALAN OFFLINE di VM Ubuntu 24.04 fresh tanpa internet. Asumsi Laravel standar + sqlite.
# Pakai: ./install-offline.sh [--skip-tests] [--no-serve] [--port 8000]
set -euo pipefail
SKIP_TESTS=0; SERVE=1; PORT=8000
while [ $# -gt 0 ]; do case "$1" in --skip-tests) SKIP_TESTS=1;; --no-serve) SERVE=0;; --port) PORT="$2"; shift;; --port=*) PORT="${1#--port=}";; esac; shift; done
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
OFF="$ROOT/deploy/offline"
cd "$ROOT"
if ls "$OFF"/apt-cache/*.deb >/dev/null 2>&1; then sudo dpkg -i "$OFF"/apt-cache/*.deb; fi
command -v php >/dev/null || { echo "php hilang (apt-cache kosong?)"; exit 1; }
php -v | head -1
[ -f "$OFF/cbt-akm-offline-bundle.tar.gz" ] || { echo "hilang: bundle tar.gz"; exit 1; }
tar -xzf "$OFF/cbt-akm-offline-bundle.tar.gz" -C "$ROOT"
[ -f .env ] || cp .env.example .env
grep -q '^DB_CONNECTION=sqlite' .env || sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env
mkdir -p database storage/framework/{sessions,views,cache} bootstrap/cache
touch database/database.sqlite
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --force 2>/dev/null || echo "WARN: seed kosong/gagal, lanjut"
[ "$SKIP_TESTS" = 1 ] || php artisan test || echo "WARN: test gagal, lanjut serve"
php artisan storage:link 2>/dev/null || true
echo "serve: http://0.0.0.0:$PORT"
[ "$SERVE" = 1 ] && php artisan serve --host=0.0.0.0 --port="$PORT"
