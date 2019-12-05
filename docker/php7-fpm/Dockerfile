# See https://github.com/docker-library/php/blob/master/7.2/fpm/Dockerfile
FROM php:7.3-fpm
ARG TIMEZONE

MAINTAINER Maria Jose Herraiz <mjherraizgarcia@gmail.com>

RUN docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install opcache
# dev
RUN  sed -i  's/;opcache.validate_timestamps=1/opcache.validate_timestamps=0/' /usr/local/etc/php/php.ini-development
RUN  sed -i  's/;opcache.memory_consumption=128/opcache.memory_consumption=256/' /usr/local/etc/php/php.ini-development
RUN  sed -i  's/;opcache.max_accelerated_files=10000/opcache.max_accelerated_files=20000/' /usr/local/etc/php/php.ini-development
RUN  sed -i  's/;opcache.save_comments=1/opcache.save_comments=1/' /usr/local/etc/php/php.ini-development
# prod
RUN  sed -i  's/;opcache.validate_timestamps=1/opcache.validate_timestamps=0/' /usr/local/etc/php/php.ini-production
RUN  sed -i  's/;opcache.memory_consumption=128/opcache.memory_consumption=256/' /usr/local/etc/php/php.ini-production
RUN  sed -i  's/;opcache.max_accelerated_files=10000/opcache.max_accelerated_files=20000/' /usr/local/etc/php/php.ini-production
RUN  sed -i  's/;opcache.save_comments=1/opcache.save_comments=1/' /usr/local/etc/php/php.ini-production

RUN apt-get update && apt-get install -y \
    openssl \
    git \
    unzip \
    vim \
    supervisor

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

RUN composer global require hirak/prestissimo

ENV COMPOSER_ALLOW_SUPERUSER 1

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone
RUN printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini
RUN "date"

# Type docker-php-ext-install to see available extensions
RUN docker-php-ext-install pdo pdo_mysql
RUN apt-get install -y libzip-dev zlib1g-dev; docker-php-ext-install zip

# Install Node
RUN curl -sL https://deb.nodesource.com/setup_6.x | bash -
RUN apt-get install -y nodejs

RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN apt-get update &&  apt-get install yarn

RUN yarn add webpack --dev

#Install wkhtmltopdf
RUN apt-get -y install wget
RUN wget https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/0.12.5/wkhtmltox_0.12.5-1.stretch_amd64.deb

RUN apt-get -y install libxrender1
RUN apt-get -y install xfonts-base
RUN apt-get -y install xfonts-75dpi
RUN apt-get -y install libjpeg62-turbo
RUN apt-get -y install fontconfig

RUN dpkg -i wkhtmltox_0.12.5-1.stretch_amd64.deb
RUN apt-get -f install
#End Install wkhtmltopdf


#instal ampq
RUN apt-get update \
    && apt-get install -y \
        librabbitmq-dev \
        libssh-dev \
    && docker-php-ext-install \
        bcmath \
        sockets \
    && pecl install amqp \
    && docker-php-ext-enable amqp

# install xdebug
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_connect_back=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.idekey=\"PHPSTORM\"" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_port=9001" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN echo 'alias sf="php bin/console"' >> ~/.bashrc

# Install GD
RUN apt-get install -y build-essential libssl-dev zlib1g-dev libpng-dev libjpeg-dev libfreetype6-dev
#RUN apt-get install -y libpng-dev
#RUN docker-php-ext-install gd
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd

RUN docker-php-ext-configure calendar && docker-php-ext-install calendar

RUN mkdir -p /var/www/symfony/var/cache
RUN mkdir -p /var/www/symfony/var/log

WORKDIR /var/www/symfony

RUN usermod -u 1000 www-data
RUN chown -R www-data:www-data /var/www/symfony/var/cache
RUN chown -R www-data:www-data /var/www/symfony/var/log
