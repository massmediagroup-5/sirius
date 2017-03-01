FROM php:5.6-fpm

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get update && apt-get install zip unzip zlib1g-dev libicu-dev git g++ -y

RUN docker-php-ext-install mysqli pdo pdo_mysql intl

RUN apt-get update && apt-get install wget bzip2 xz-utils -y
RUN wget https://nodejs.org/dist/v6.9.4/node-v6.9.4-linux-x64.tar.xz
RUN tar -C /usr/local --strip-components 1 -xJf node-v6.9.4-linux-x64.tar.xz

RUN npm install -g gulp

RUN apt-get update && apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libssl-dev libreadline-dev libgdbm-dev && \
docker-php-ext-install exif mbstring zip && \
docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/  &&  \
docker-php-ext-install gd

# Ruby and compass
RUN wget https://cache.ruby-lang.org/pub/ruby/2.2/ruby-2.2.6.tar.gz && \
tar -xzf ruby-2.2.6.tar.gz && \
cd ruby-2.2.6 && \
./configure --prefix=/usr/local && \
make && \
make install
RUN gem update --system && gem install bundle capistrano compass --no-ri --no-rdoc

ADD php.ini /usr/local/etc/php/conf.d/

ENV user sirius
ARG PUID=1000
ARG PGID=1000
RUN groupadd -g $PGID $user && \
    useradd -u $PUID -g $user -m $user

RUN chown -R $user:$user /usr/local/

USER $user

WORKDIR /var/www
