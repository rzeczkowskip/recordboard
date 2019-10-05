FROM php:7.4-rc-fpm

RUN apt-get update && apt-get install -y \
 libfreetype6-dev libpng-dev libjpeg62-turbo-dev \
 libxml2-dev \
 zlib1g-dev unzip libzip-dev \
 libmagickwand-dev \
 libxslt-dev

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/
RUN docker-php-ext-install bcmath gd intl pdo_mysql zip opcache xsl

RUN pecl install -s -o imagick && \
    pecl install -s -o redis && \
    pecl install -s -o "xdebug-2.8.0beta1"

RUN docker-php-ext-enable redis imagick

RUN echo "memory_limit=1024M" > "$PHP_INI_DIR/conf.d/memory-limit.ini" \
 && echo "date.timezone=${PHP_TIMEZONE:-UTC}" > "$PHP_INI_DIR/conf.d/date_timezone.ini"

RUN curl https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN mkdir /.composer && chmod 777 /.composer
