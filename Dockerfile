FROM php:8.3-fpm AS base

WORKDIR /var/www/html
LABEL Name=kkuproject Version=0.0.1
RUN apt-get -y update && apt-get install -y fortunes
CMD ["sh", "-c", "/usr/games/fortune -a | cowsay"]
