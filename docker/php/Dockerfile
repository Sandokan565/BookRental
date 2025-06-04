# Base image with PHP and FPM
FROM php:8.4.4-fpm

# Install PHP extensions and dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create a non-root user (APPLY AFTER APP CREATION AND REBUILD)
RUN useradd -u 1000 -m tomas
RUN chown -R tomas:www-data /var/www/html
RUN chmod -R 775 /var/www/html

# Switch to non-root user (APPLY AFTER APP CREATION AND REBUILD)
USER tomas

# Check if Laravel project folders exist before setting permissions
RUN if [ -d "/var/www/html/storage" ]; then \
        chown -R tomas:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
        chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache; \
    fi