FROM php:8.2-fpm-alpine

# Cài đặt các gói hệ thống cần thiết (Nginx, Supervisor, gettext cho envsubst, thư viện đồ họa và zip)
RUN apk update && apk add --no-cache \
    nginx \
    supervisor \
    curl \
    gettext \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev

# Cài đặt các PHP Extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip

# Cài đặt Composer từ image chính thức
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Thiết lập thư mục làm việc
WORKDIR /var/www/html

# Copy toàn bộ mã nguồn vào container
COPY . /var/www/html

# Cài đặt các package composer ở chế độ production (tối ưu hóa autoload)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy các file cấu hình Docker
COPY docker/nginx.conf /etc/nginx/conf.d/nginx.conf.template
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

# Cấp quyền cho user www-data
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Cổng mặc định của Render
EXPOSE 10000

# Khởi chạy container qua entrypoint script
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
