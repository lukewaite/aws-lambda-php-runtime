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


RUN \
    curl -sL http://php.net/distributions/php-7.0.33.tar.gz | tar -xvz

RUN \
    mkdir -p /tmp/php-7-bin \
    && cd php-7.0.33 \
    && ./configure --prefix /tmp/php-7-bin --with-openssl=/usr/local/ssl --with-curl --with-zlib --enable-mbstring \
    && make install
