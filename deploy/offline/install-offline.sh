#!/usr/bin/env bash
# install-offline.sh — JALAN OFFLINE di VM Ubuntu 24.04 fresh tanpa internet. Default MySQL, sqlite opsional.
# Pakai: ./install-offline.sh [--db=mysql|sqlite] [--skip-tests] [--no-serve] [--port 8000]
set -euo pipefail
DB=mysql; SKIP_TESTS=0; SERVE=1; PORT=8000
while [ $# -gt 0 ]; do case "$1" in
  --db) DB="$2"; shift;;
  --db=*) DB="${1#--db=}";;
  --skip-tests) SKIP_TESTS=1;;
  --no-serve) SERVE=0;;
  --port) PORT="$2"; shift;;
  --port=*) PORT="${1#--port=}";;
  *) echo "flag tak dikenal: $1"; exit 1;;
esac; shift; done
[ "$DB" = mysql ] || [ "$DB" = sqlite ] || { echo "--db harus mysql|sqlite"; exit 1; }
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
OFF="$ROOT/deploy/offline"
cd "$ROOT"
if ls "$OFF"/apt-cache/*.deb >/dev/null 2>&1; then sudo dpkg -i "$OFF"/apt-cache/*.deb; fi
command -v php >/dev/null || { echo "php hilang (apt-cache kosong?)"; exit 1; }
php -v | head -1
[ -f "$OFF/cbt-akm-offline-bundle.tar.gz" ] || { echo "hilang: bundle tar.gz"; exit 1; }
tar -xzf "$OFF/cbt-akm-offline-bundle.tar.gz" -C "$ROOT"
[ -f .env ] || cp .env.example .env
envset() { grep -q "^$1=" .env && sed -i "s|^$1=.*|$1=$2|" .env || echo "$1=$2" >> .env; }
if [ "$DB" = mysql ]; then
  command -v mysqld >/dev/null || { echo "mysql-server hilang (bundle-online.sh belum update?)"; exit 1; }
  php -m | grep -qi pdo_mysql || { echo "php8.3-mysql hilang"; exit 1; }
  sudo service mysql start 2>/dev/null || sudo systemctl start mysql 2>/dev/null || sudo mysqld --daemonize 2>/dev/null || true
  for i in $(seq 1 30); do sudo mysql -e 'SELECT 1' >/dev/null 2>&1 && break; sleep 1; done
  envset DB_CONNECTION mysql
  grep -q '^DB_HOST=' .env || envset DB_HOST 127.0.0.1
  grep -q '^DB_PORT=' .env || envset DB_PORT 3306
  grep -q '^DB_DATABASE=' .env || envset DB_DATABASE cbt_akm
  grep -q '^DB_USERNAME=' .env || envset DB_USERNAME root
  grep -q '^DB_PASSWORD=' .env || envset DB_PASSWORD ""
  # shellcheck disable=SC1091
  set -a; . ./.env; set +a
  DB_DATABASE="${DB_DATABASE:-cbt_akm}"; DB_USERNAME="${DB_USERNAME:-root}"; DB_PASSWORD="${DB_PASSWORD:-}"
  sudo mysql -e "CREATE DATABASE IF NOT EXISTS \`$DB_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
  if [ -n "$DB_USERNAME" ] && [ "$DB_USERNAME" != root ]; then
    sudo mysql -e "CREATE USER IF NOT EXISTS '$DB_USERNAME'@'localhost'; ALTER USER '$DB_USERNAME'@'localhost' IDENTIFIED BY '$DB_PASSWORD'; GRANT ALL ON \`$DB_DATABASE\`.* TO '$DB_USERNAME'@'localhost'; FLUSH PRIVILEGES;" 2>/dev/null ||     sudo mysql -e "CREATE USER IF NOT EXISTS '$DB_USERNAME'@'localhost' IDENTIFIED BY '$DB_PASSWORD'; GRANT ALL ON \`$DB_DATABASE\`.* TO '$DB_USERNAME'@'localhost'; FLUSH PRIVILEGES;"
  fi
else
  envset DB_CONNECTION sqlite
  mkdir -p database
  touch database/database.sqlite
fi
mkdir -p storage/framework/{sessions,views,cache} bootstrap/cache
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --force 2>/dev/null || echo "WARN: seed kosong/gagal, lanjut"
[ "$SKIP_TESTS" = 1 ] || php artisan test || echo "WARN: test gagal, lanjut serve"
php artisan storage:link 2>/dev/null || true
echo "db=$DB serve: http://0.0.0.0:$PORT"
[ "$SERVE" = 1 ] && php artisan serve --host=0.0.0.0 --port="$PORT"
