FROM php:5.6-apache

MAINTAINER Ivo Bathke <ivo.bathke@gmail.com>

ADD docker-php-pecl-install /usr/local/bin/

ADD ./ioncube/ioncube_loader_lin_5.6.so /usr/local/lib/php/extensions/no-debug-non-zts-20131226/ioncube_loader_lin_5.6.so
ADD ./ioncube/02_ioncube.ini /usr/local/etc/php/conf.d/02_ioncube.ini

ADD ./zendguardloader/opcache.so /usr/local/lib/php/extensions/no-debug-non-zts-20131226/opcache.so
ADD ./zendguardloader/ZendGuardLoader.so /usr/local/lib/php/extensions/no-debug-non-zts-20131226/ZendGuardLoader.so
ADD ./zendguardloader/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

RUN chmod u+x /usr/local/bin/docker-php-pecl-install
RUN apt-get update
RUN apt-get install -y libssl-dev libxml2-dev zlib1g-dev libicu-dev libjpeg-dev libpng-dev libmcrypt-dev wget imagemagick 

RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install json
RUN docker-php-ext-install mysql
RUN docker-php-ext-install mysqli
RUN docker-php-ext-configure gd --with-jpeg-dir=/usr/lib/x86_64-linux-gnu --with-png-dir=/usr/lib/x86_64-linux-gnu
RUN docker-php-ext-install gd
RUN docker-php-ext-install mcrypt
RUN docker-php-ext-install soap
RUN docker-php-ext-install zip

# XDebug for PHP 5
#RUN docker-php-pecl-install xdebug-2.5.5
#RUN sed -i '1 a xdebug.remote_autostart=true' /usr/local/etc/php/conf.d/docker-php-pecl-xdebug.ini
#RUN sed -i '1 a xdebug.remote_mode=req' /usr/local/etc/php/conf.d/docker-php-pecl-xdebug.ini
#RUN sed -i '1 a xdebug.remote_handler=dbgp' /usr/local/etc/php/conf.d/docker-php-pecl-xdebug.ini
#RUN sed -i '1 a xdebug.remote_connect_back=1 ' /usr/local/etc/php/conf.d/docker-php-pecl-xdebug.ini
#RUN sed -i '1 a xdebug.remote_port=9000' /usr/local/etc/php/conf.d/docker-php-pecl-xdebug.ini
#RUN sed -i '1 a xdebug.remote_host=127.0.0.1' /usr/local/etc/php/conf.d/docker-php-pecl-xdebug.ini
#RUN sed -i '1 a xdebug.remote_enable=1' /usr/local/etc/php/conf.d/docker-php-pecl-xdebug.ini

RUN a2enmod rewrite

COPY php.ini /usr/local/etc/php/
COPY apache2.conf /etc/apache2/apache2.conf

WORKDIR /var/www/
