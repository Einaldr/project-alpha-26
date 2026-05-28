FROM dunglas/frankenphp:1-alpine

# Disable HTTPS inside the container for local development. 
# It will listen on standard port 80 instead.
ENV SERVER_NAME=:80

# Install system utilities needed by Composer
RUN apk add --no-cache git unzip zip

# Install required PHP extensions for Laravel
RUN install-php-extensions pdo_mysql gd zip intl opcache pcntl bcmath

# Copy Composer from the official composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy application files (overwritten by Docker Compose volume in dev, used in prod)
COPY . /app

EXPOSE 80