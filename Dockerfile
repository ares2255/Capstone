FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

# Install Apache, PHP, and mysqli
RUN apt-get update && apt-get install -y \
    apache2 \
    php \
    php-mysqli \
    libapache2-mod-php \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable rewrite
RUN a2enmod rewrite

# Copy app files
COPY . /var/www/html/
RUN rm -f /var/www/html/index.html

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Apache config
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/app.conf \
    && a2enconf app

COPY start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]
