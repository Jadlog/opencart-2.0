#
# Dockerfile for opencart
#
# cd opencart_local
# docker build -t opencart_local .

FROM php:5.6.32-apache
MAINTAINER sistemas <sistemas@jadlog.com.br>

RUN a2enmod rewrite

RUN set -xe \
    && apt-get update \
    && apt-get install -y \
       libfreetype6-dev \
       libjpeg62-turbo-dev \
       libmcrypt-dev \
       libpng12-dev \
       libicu-dev \
       libxml2-dev \
       zlib1g-dev \
       vim \
       wget \
       unzip \
       git \
    && docker-php-ext-install -j$(nproc) iconv intl xml soap mcrypt opcache pdo pdo_mysql mysqli mbstring zip\
    && docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-enable mcrypt \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

ENV OPENCART_VER 2.0.3.1
ENV OPENCART_MD5 222c20ee61a4ff280f55fb0ea688c746
ENV OPENCART_URL https://github.com/opencart/opencart/archive/${OPENCART_VER}.tar.gz
ENV OPENCART_FILE opencart.tar.gz
ENV VQMOD_URL https://github.com/vqmod/vqmod/releases/download/v2.6.3-opencart/vqmod-2.6.3-opencart.zip
ENV VQMOD_FILE vqmod.zip

RUN set -xe \
    && curl -sSL ${OPENCART_URL} -o ${OPENCART_FILE} \
    && echo "${OPENCART_MD5}  ${OPENCART_FILE}" | md5sum -c \
    && tar xzf ${OPENCART_FILE} --strip 2 --wildcards '*/upload/' \
    && mv config-dist.php config.php \
    && mv admin/config-dist.php admin/config.php \
    && rm ${OPENCART_FILE} \
    && curl -sSL ${VQMOD_URL} -o ${VQMOD_FILE} \
    && unzip ${VQMOD_FILE} \
    && rm ${VQMOD_FILE} \
    && chown -R www-data:www-data /var/www
