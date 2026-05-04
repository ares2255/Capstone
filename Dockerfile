FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    apache2 \
    php \
    php-mysqli \
    libapache2-mod-php \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Remove ALL default apache files
RUN rm -rf /var/www/html/*

# Copy app files
COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

RUN echo '<Directory /var/www/html>' >> /etc/apache2/conf-available/app.conf \
    && echo '    AllowOverride All' >> /etc/apache2/conf-available/app.conf \
    && echo '    Require all granted' >> /etc/apache2/conf-available/app.conf \
    && echo '</Directory>' >> /etc/apache2/conf-available/app.conf \
    && a2enconf app

EXPOSE 80

CMD ["apachectl", "-D", "FOREGROUND"]
