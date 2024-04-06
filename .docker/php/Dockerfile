FROM php:8.1-fpm

RUN docker-php-ext-install pdo_mysql

COPY BE/ /var/www/html/

WORKDIR /var/www/html

EXPOSE 8181

CMD ["php-fpm"]