# Use official PHP image with Apache
FROM php:8.2-apache

# Enable mod_rewrite (useful for URLs and .htaccess)
RUN a2enmod rewrite

# Copy all your project files to the container
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

# Set working directory
WORKDIR /var/www/html

# Expose port 80 so web server is accessible
EXPOSE 80
