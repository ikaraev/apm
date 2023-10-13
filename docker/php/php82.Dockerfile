FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
  libzip-dev \
  libonig-dev \
  cron \
  libfreetype6-dev \
  libicu-dev \
  libjpeg62-turbo-dev \
  libmcrypt-dev \
  libxslt1-dev \
  zip \
  libpng-dev \
  libxml2-dev \
  libxslt-dev \
  curl \
  vim \
  wget \
  git \
  procps

RUN apt-get install -y libcurl4-openssl-dev
RUN apt-get install -y libsodium-dev

RUN docker-php-ext-install pdo \
  pdo_mysql \
  mysqli \
  bcmath \
  ctype \
  curl \
  dom \
  fileinfo \
  gd \
  iconv \
  intl \
  mbstring \
  opcache \
  pdo_mysql \
  simplexml \
  soap \
  sockets \
  sodium \
  xmlwriter \
  xml \
  xsl \
  zip

RUN docker-php-ext-install pcntl

#RUN apt-get install -y php8.2-xdebug

# RUN set -x \
#     && pecl install -f xdebug-2.9.8 \
#     && docker-php-ext-enable xdebug

RUN docker-php-ext-install -j$(nproc) iconv
#RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd

RUN curl -sS https://getcomposer.org/installer | \
  php -- --install-dir=/usr/local/bin --filename=composer --version=2.2.18

# Downgrade to composer version 2.1.6.
# Magento 2.4.4 works only with composer v.2.1
#RUN composer self-update 2.1.6

# Install XDEBUG extension
# Xdebug replaces PHP's var_dump() function for displaying variables.
# https://xdebug.org/download.php
# confirm => php -m | grep -i xdebug
RUN set -x \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

ENV PHP_MEMORY_LIMIT 2G
ENV PHP_MAX_EXECUTION_TIME=300
ENV PHP_POST_MAX_SIZE=500M
ENV PHP_UPLOAD_MAX_FILESIZE=1024M
ENV PHP_MAX_INPUT_VARS=10000

# Magento DEVELOPMENT permissions (this may take a while...)
RUN set -x \
    && cd /var/www/html \
    && rm -rf ./generated/metadata/* ./generated/code/* ./pub/static/* ./var/cache/* ./var/page_cache/* ./var/view_preprocessed/* ./var/log/*
    #&& find var generated vendor pub/static pub/media app/etc -type f -exec chmod g+w {} \; && find var generated vendor pub/static pub/media app/etc -type d -exec chmod g+ws {} \; && chmod u+x bin/magento

RUN chown -R www-data:www-data /var/www \
  && chmod -R g+rwX /var/www \
  && chmod -R g+s /var/www
# RUN chmod -R g+rwX /var/www
# RUN chmod -R g+s /var/www

ARG user_id=1000
RUN usermod -u $user_id www-data \
  && groupmod -g $user_id www-data
# RUN groupmod -g $user_id www-data

# Set working directory
WORKDIR /var/www/html

# Expose port 9000 and start php-fpm server
EXPOSE 9000

CMD ["php-fpm"]
