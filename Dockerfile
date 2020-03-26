FROM php:7.2-fpm
RUN apt-get -y update && apt-get -y install wget && apt-get -y install unzip
RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php -- --quiet
RUN mv composer.phar /usr/bin/composer
COPY . /web
WORKDIR /web
RUN apt-get install -y libcurl4-openssl-dev pkg-config libssl-dev
RUN pecl install mongodb && docker-php-ext-enable mongodb
RUN apt-get -y install git && apt-get -y install nginx
COPY default /etc/nginx/sites-enabled/default
RUN chmod -R 777 storage bootstrap
RUN composer install
CMD nginx; php-fpm;
