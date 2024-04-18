FROM php:8.1.18-fpm

# PHP 設定ファイル
# COPY php.ini /etc/php.ini
COPY php.ini /usr/local/etc/php/

# Composerのインストール
RUN cd /usr/bin && curl -s http://getcomposer.org/installer | php && ln -s /usr/bin/composer.phar /usr/bin/composer

# ミドルウェアインストール
RUN apt update \
  && apt upgrade -y \
  && apt install -y \
        apt-transport-https \
        libfreetype6-dev \
        libicu-dev \
        apt-utils \
        build-essential \
        debconf-utils \
        libjpeg62-turbo-dev \
        libpng-dev \
        gcc \
        git \
        zip \
        unzip \
        vim \
        gnupg2 \
        libpng-dev \
        libpq-dev \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        wget \
        curl \
        msmtp \
        msmtp-mta \
        mariadb-client \
        rsync \
        locales \
        ssl-cert \
        zlib1g-dev \
        libwebp-dev \
        gnupg \
        iputils-ping \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) zip gd mysqli pdo pdo_mysql intl\
    && apt upgrade -y ca-certificates \
    && echo "en_US.UTF-8 UTF-8" >/etc/locale.gen \
    && locale-gen

# タイムゾーン設定
RUN ln -sf  /usr/share/zoneinfo/Asia/Tokyo /etc/localtime

WORKDIR /var/www