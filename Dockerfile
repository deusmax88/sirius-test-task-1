FROM php:cli

RUN docker-php-ext-install pdo pdo_mysql

ADD ./app /app

WORKDIR /app

CMD php index.php