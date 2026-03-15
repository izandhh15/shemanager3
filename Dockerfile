FROM php:8.4-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libzip-dev libpng-dev libonig-dev libxml2-dev \
    nodejs npm \
    && docker-php-ext-install pdo pdo_pgsql pdo_sqlite mbstring zip gd intl bcmath pcntl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy composer files first for layer caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy package files and install JS deps
COPY package.json package-lock.json ./
RUN npm ci

# Copy the rest of the app
COPY . .

# Build frontend assets
RUN npm run build

# Set permissions
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views \
    && chmod -R 775 storage bootstrap/cache

# Run composer scripts after full copy
RUN composer dump-autoload --optimize

EXPOSE 8080

CMD ["bash", "railway-start.sh"]
