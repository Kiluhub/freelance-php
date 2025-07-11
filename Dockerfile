# Use official PHP with Apache
FROM php:8.2-apache

# Enable URL rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy contents of 'applic/' to web root
COPY applic/ /var/www/html/

# Set permissions (optional, but safe)
RUN chown -R www-data:www-data /var/www/html

# Expose HTTP port
EXPOSE 80
