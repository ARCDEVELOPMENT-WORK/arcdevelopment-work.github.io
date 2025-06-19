# Use official PHP image with Apache
FROM php:8.0-apache

# Set ServerName to suppress warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copy project files to Apache document root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Expose port 80
EXPOSE 80

# Enable Apache mod_rewrite (optional, if needed)
RUN a2enmod rewrite

# Set permissions (optional, adjust as needed)
RUN chown -R www-data:www-data /var/www/html

# Start Apache in foreground (default command)
CMD ["apache2-foreground"]
