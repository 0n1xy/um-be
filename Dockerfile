# Chọn image PHP phù hợp (fpm-alpine nhẹ hơn)
FROM php:8.2-fpm-alpine

# Cài đặt các dependencies cần thiết
RUN apk add --no-cache \
    php8-cli \
    php8-mbstring \
    php8-tokenizer \
    php8-xml \
    php8-curl \
    php8-openssl \
    php8-pdo \
    php8-pdo_mysql \
    php8-bcmath \
    php8-zip \
    php8-fileinfo \
    php8-ctype \
    php8-json \
    php8-phar \
    php8-session \
    php8-simplexml \
    php8-dom \
    php8-iconv \
    php8-xmlwriter \
    php8-xmlreader \
    php8-posix \
    php8-intl \
    composer \
    && docker-php-ext-install pdo_mysql

# Đặt thư mục làm việc
WORKDIR /app

# Copy toàn bộ project vào container
COPY . /app

# Cài đặt Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Tạo .env và tạo APP_KEY, JWT_SECRET
RUN cp .env.example .env && \
    php artisan key:generate --force && \
    php artisan jwt:secret --force && \
    php artisan config:clear && \
    php artisan cache:clear && \
    php artisan migrate --force

# Expose cổng 8000
EXPOSE 8000

# Chạy Laravel server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
