# Use official PHP image with Apache
FROM php:8.2-apache

# Enable mod_rewrite for .htaccess support (if needed)
RUN a2enmod rewrite

# Ensure Apache looks for index.php or index.html by default
RUN echo "DirectoryIndex index.php index.html" >> /etc/apache2/apache2.conf

# Copy the contents of applic/ into the Apache root
COPY applic/ /var/www/html/

# Set permissions (important for PHP execution)
RUN chown -R www-data:www-data /var/www/html

# Set working directory
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80
