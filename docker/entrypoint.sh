#!/bin/sh
set -e

# Gán cổng mặc định là 10000 nếu Render không truyền biến PORT
export PORT="${PORT:-10000}"

echo "Starting container on port ${PORT}..."

# Thay thế biến ${PORT} vào cấu hình Nginx
envsubst '${PORT}' < /etc/nginx/conf.d/nginx.conf.template > /etc/nginx/http.d/default.conf

# Đảm bảo quyền ghi cho storage và cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Tạo storage link nếu chưa có
php /var/www/html/artisan storage:link || true

# Xóa cache cũ để nhận biến môi trường mới từ Render
php /var/www/html/artisan optimize:clear || true

# Khởi chạy Supervisord để chạy đồng thời Nginx, PHP-FPM và Queue Worker
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
