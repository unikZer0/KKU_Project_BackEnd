# ------------------------- Base PHP Image -------------------------
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

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --prefer-dist

# Copy the Laravel app
COPY . .

# Storage link and permissions
RUN php artisan storage:link \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ------------------------- Node Build Stage -------------------------
FROM base AS node-build

# Install Node.js dependencies
RUN apt-get update && apt-get install -y curl gnupg lsb-release ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Setup Node.js 20.x repo and install
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get update && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Copy package.json & package-lock.json
COPY package*.json ./

# Install npm dependencies & build assets
RUN npm install --verbose \
    && npm run build

# ------------------------- Final Stage -------------------------
FROM php:8.3-fpm AS final

WORKDIR /var/www/html

# Install MySQL PDO extension (important!)
RUN apt-get update && apt-get install -y default-mysql-client \
    && docker-php-ext-install pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Copy Laravel app from base stage
COPY --from=base /var/www/html /var/www/html

# Copy built assets from node-build stage
COPY --from=node-build /var/www/html/public /var/www/html/public
COPY --from=node-build /var/www/html/resources /var/www/html/resources

# Copy Nginx config
COPY ./nginx/default.conf /etc/nginx/conf.d/default.conf

# Set permissions again
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
