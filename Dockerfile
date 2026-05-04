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

COPY . /var/www/html/
RUN rm -f /var/www/html/index.html

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

RUN echo '<Directory /var/www/html>' >> /etc/apache2/conf-available/app.conf \
    && echo '    AllowOverride All' >> /etc/apache2/conf-available/app.conf \
    && echo '    Require all granted' >> /etc/apache2/conf-available/app.conf \
    && echo '</Directory>' >> /etc/apache2/conf-available/app.conf \
    && a2enconf app

EXPOSE 80

CMD bash -c "sed -i \"s/Listen 80/Listen \${PORT:-80}/\" /etc/apache2/ports.conf && sed -i \"s/<VirtualHost \*:80>/<VirtualHost *:\${PORT:-80}>/\" /etc/apache2/sites-enabled/000-default.conf && apachectl -D FOREGROUND"
