# ------------------------- Base PHP Image -------------------------
# This stage installs all PHP and system dependencies
FROM php:8.3-fpm AS base

WORKDIR /var/www/html

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev zip curl unzip git nano libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# Copy only composer files to leverage Docker caching
COPY composer.json composer.lock ./

# Install Composer dependencies for production
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --prefer-dist

# Copy the rest of the Laravel application
COPY . .

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ------------------------- Node Build Stage -------------------------
# This stage builds the frontend assets and is then discarded
FROM base AS node-build

# Install Node.js
RUN apt-get update && apt-get install -y curl gnupg \
    && rm -rf /var/lib/apt/lists/* \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Copy package files
COPY package*.json ./

# Install npm dependencies & build the assets for production
RUN npm install \
    && npm run build

# ------------------------- Final Production Stage -------------------------
# This is the final, clean image that will run in production
# It starts from 'base' to reuse all the PHP and Composer layers
FROM base AS final

# Copy only the compiled assets from the 'node-build' stage
# The 'public/build' directory contains the final CSS and JS files
COPY --from=node-build /var/www/html/public/build /var/www/html/public/build

# Expose port and start php-fpm
EXPOSE 9000
CMD ["php-fpm"]
