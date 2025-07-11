# Use official PHP with Apache
FROM php:8.2-apache

# Enable mod_rewrite (if you're using pretty URLs or .htaccess)
RUN a2enmod rewrite

# ✅ Install PDO and PostgreSQL PDO driver
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Set working directory
WORKDIR /var/www/html

# Copy your app code (update path if needed)
COPY applic/ /var/www/html/

# Fix permissions (optional but good practice)
RUN chown -R www-data:www-data /var/www/html

# Expose web port
EXPOSE 80
