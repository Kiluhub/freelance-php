# Use official PHP with Apache
FROM php:8.2-apache

# Enable mod_rewrite
RUN a2enmod rewrite

# ✅ Install PDO MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

# Set working directory
WORKDIR /var/www/html

# Copy your app code (if it's in "applic" folder)
COPY applic/ /var/www/html/

# Fix permissions
RUN chown -R www-data:www-data /var/www/html

# Expose web port
EXPOSE 80
