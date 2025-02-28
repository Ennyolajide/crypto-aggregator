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
    supervisor \
    procps \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring zip gd pcntl \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js 20 (for Laravel Mix or Vite)
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - && apt-get install -y nodejs

# Copy Laravel project files
COPY . .

# COPY .env.testing .env.testing

# COPY .env.example .env.example

# RUN mv .env.example .env

# Ensure proper permissions before running installation
RUN chown -R www-data:www-data storage
RUN chmod -R 770 storage

# Ensure start.sh is executable
RUN chmod +x /var/www/start.sh

# Copy Supervisor config for running multiple processes
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose only the Laravel app on port 13579
EXPOSE 80 7373

# Start PHP-FPM server using PHP's built-in server
CMD ["php", "-S", "0.0.0.0:80", "-t", "/var/www/public"]
