FROM php:7.4-apache

RUN apt-get update \
  && apt-get install -y libzip-dev git wget --no-install-recommends \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN wget https://getcomposer.org/download/2.0.9/composer.phar \
    && mv composer.phar /usr/bin/composer && chmod +x /usr/bin/composer

COPY . /var/www
COPY apache.conf /etc/apache2/sites-enabled/000-default.conf
COPY entrypoint.sh /entrypoint.sh
WORKDIR /var/www

RUN chmod +x /entrypoint.sh
CMD ["apache2-foreground"]
ENTRYPOINT ["/entrypoint.sh"]