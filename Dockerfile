FROM php:8.3-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo_mysql zip

# Enable Apache Rewrite Module for clean MVC URLs
RUN a2enmod rewrite

# Copy custom Apache site configuration
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Set proper working directory
WORKDIR /var/www/html

# Expose HTTP port
EXPOSE 80
