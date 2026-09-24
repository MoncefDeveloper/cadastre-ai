#!/bin/sh
set -e

echo "🚀 [Cadastre AI] Booting container..."

# 1. Hydrate baseline placeholder assets if persistent volume is fresh
if [ ! -d "/var/www/html/storage/app/public/properties" ]; then
    echo "📦 [Hydration] Fresh persistent volume detected. Copying baseline demo assets..."
    mkdir -p /var/www/html/storage/app/public
    cp -rn /var/www/html/storage_defaults/* /var/www/html/storage/app/
fi

# 2. Guarantee proper directory structure
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

# 3. Enforce strict permissions for web server (www-data)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 4. Generate storage symlink if missing
if [ ! -L "/var/www/html/public/storage" ]; then
    echo "🔗 Creating storage symlink..."
    php /var/www/html/artisan storage:link || true
fi

echo "✅ [Cadastre AI] Ready. Handing over to Supervisord..."

# 5. Execute the container's main command (Supervisord)
exec "$@"
