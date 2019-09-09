# Unlike most docker images, where we want to optimize layers to be small and few
# we'd rather leverage layer caching for faster builds when things change, since
# we never ship this image, rather we extract the compiled binaries as part of
# our build process.

FROM amazonlinux:2018.03

WORKDIR /tmp

RUN \
    yum install autoconf bison gcc gcc-c++ libcurl-devel libxml2-devel openssl-devel -y

ENV PHP_VERSION 7.3.7

RUN \
    curl -sL http://php.net/distributions/php-${PHP_VERSION}.tar.gz | tar -xvz

RUN \
    mkdir -p /tmp/php-7-bin \
    && cd php-${PHP_VERSION} \
    && ./configure --prefix /tmp/php-7-bin --with-openssl --with-curl --with-zlib --enable-mbstring \
    && make install
