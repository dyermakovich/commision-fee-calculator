FROM php:8.0-apache

RUN apt-get update \
  && apt-get install -y zip

# Install composer

ENV COMPOSER_HOME /composer
ENV PATH ./vendor/bin:/composer/vendor/bin:$PATH
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer

# Setup working directory
WORKDIR /var/www/html

# Run composer install & autoload
COPY composer.json composer.json
RUN composer install
RUN composer dump-autoload -o