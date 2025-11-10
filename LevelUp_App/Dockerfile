FROM php:8.2-cli

# System deps
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer (global)
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
 && php -r "unlink('composer-setup.php');"

# Node for Vite/HMR
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
 && apt-get update && apt-get install -y nodejs \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy only composer files first
COPY composer.json composer.lock* ./
RUN composer install --no-scripts --no-interaction --prefer-dist || true

# Copy the rest of the app
COPY . .

# Ensure storage perms for Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose PHP dev server port
EXPOSE 8000

# Default command:
# 1) install PHP deps if needed
# 2) install node deps if needed
# 3) start PHP dev server listening on all interfaces for Docker
CMD bash -lc '\
  if [ ! -f vendor/autoload.php ]; then composer install --no-interaction; fi && \
  if [ -f package.json ]; then \
    if [ ! -d node_modules ]; then npm install; fi; \
  fi && \
  php artisan key:generate --force || true && \
  php artisan serve --host=0.0.0.0 --port=8000 \
'
