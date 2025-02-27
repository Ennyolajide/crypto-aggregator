# Use official PHP image with necessary extensions
FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www

# Install necessary system dependencies and PHP extensions for Laravel
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    build-essential \
    pkg-config \
    libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring zip gd pcntl \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js 20 (for Laravel Mix or Vite)
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - && apt-get install -y nodejs

# Copy Laravel project files
COPY . .

# Ensure proper permissions before running installation
RUN chmod -R 777 /var/www/storage /var/www/bootstrap/cache /var/www/public

# RUN touch /var/www/database/testing.sqlite && chmod 777 /var/www/database/testing.sqlite

RUN chmod +x /var/www/start.sh

# Expose port 80 internally for the app (to be mapped to 13579)
EXPOSE 80

# Start PHP-FPM server using PHP's built-in server
CMD ["php", "-S", "0.0.0.0:80", "-t", "/var/www/public"]