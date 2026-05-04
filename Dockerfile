FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy all app files to web root
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Apache config
RUN echo '<Directory /var/www/html>\nAllowOverride All\nRequire all granted\n</Directory>' \
    > /etc/apache2/conf-available/app.conf \
    && a2enconf app

# Copy startup script
COPY start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
