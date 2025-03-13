#!/bin/sh

# Chờ MySQL khởi động
echo "Waiting for MySQL..."
until nc -z -v -w30 um_mysql 3306; do
  echo "Waiting for MySQL to start..."
  sleep 5
done
echo "MySQL is up and running!"

# Chạy các lệnh Laravel setup
php artisan key:generate
php artisan migrate --seed --force
php artisan jwt:secret --force

# Khởi động PHP-FPM
exec php-fpm
