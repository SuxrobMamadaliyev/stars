#!/bin/bash
# Exit on error
set -o errexit

# Create necessary directories
mkdir -p public_html/storage/logs
mkdir -p public_html/storage/cache

# Set proper permissions
chmod -R 755 public_html/storage

# Install PHP and required extensions for Render
if [ -n "$RENDER" ]; then
    # For Render environment
    apt-get update && apt-get install -y \
        php8.1 \
        php8.1-common \
        php8.1-curl \
        php8.1-mbstring \
        php8.1-pgsql \
        php8.1-xml \
        php8.1-zip \
        composer
        
    # Install PostgreSQL client (useful for migrations)
    apt-get install -y postgresql-client
    
    # Install any PHP dependencies via Composer if needed
    if [ -f "composer.json" ]; then
        composer install --no-dev --optimize-autoloader --no-interaction
    fi
    
    # Run database migrations if you have them
    # php public_html/tools/migrate.php
    
    # Clear any cached files
    if [ -d "public_html/storage/cache" ]; then
        rm -rf public_html/storage/cache/*
    fi
    
    # Generate application key if needed
    # php public_html/artisan key:generate --force
    
    # Optimize the application
    # php public_html/artisan optimize
    
    echo "Build completed successfully on Render"
else
    echo "Local development environment detected. Skipping production build steps."
fi
