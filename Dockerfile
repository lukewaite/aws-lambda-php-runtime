# Unlike most docker images, where we want to optimize layers to be small and few
# we'd rather leverage layer caching for faster builds when things change, since
# we never ship this image, rather we extract the compiled binaries as part of
# our build process.

FROM amazonlinux:2017.03

WORKDIR /tmp

RUN \
    yum install autoconf bison gcc gcc-c++ libcurl-devel libxml2-devel -y

RUN \
    curl -sL http://www.openssl.org/source/openssl-1.0.1k.tar.gz | tar -xvz \
    && cd openssl-1.0.1k \
    && ./config \
    && make \
    && make install

ENV PHP_VERSION 7.3.0

RUN \
    curl -sL http://php.net/distributions/php-${PHP_VERSION}.tar.gz | tar -xvz

RUN \
    mkdir -p /tmp/php-7-bin \
    && cd php-${PHP_VERSION} \
    && ./configure --prefix /tmp/php-7-bin --with-openssl=/usr/local/ssl --with-curl --with-zlib --enable-mbstring \
    && make install
