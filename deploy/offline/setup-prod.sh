#!/usr/bin/env bash
# setup-prod.sh — Nginx+FPM+MySQL produksi offline. Jalankan sekali sebagai root di VM.
set -euo pipefail
APP=/var/www/cbt-akm
systemctl enable --now php8.3-fpm nginx mysql
mysql -e "CREATE DATABASE IF NOT EXISTS `cbt_akm` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
cd "$APP"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link 2>/dev/null || true
chown -R www-data:www-data storage bootstrap/cache
systemctl reload php8.3-fpm nginx
echo OK
