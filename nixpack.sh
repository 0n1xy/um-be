# Nixpacks build environment for Laravel on Railway

build:
  # Cài đặt dependencies
  builder: "nixpacks"
  plan:
    - php
    - composer
    - nodejs
    - mysql-client

  # Cài đặt Laravel dependencies
  preinstall:
    - composer install --no-dev --optimize-autoloader

  # Chạy migration & seed database
  prestart:
    - php artisan key:generate
    - php artisan jwt:secret
    - php artisan migrate --seed

  # Command để chạy server Laravel
  start:
    cmd: "php artisan serve --host=0.0.0.0 --port=${PORT}"
