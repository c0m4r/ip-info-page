FROM alpine:3.19
WORKDIR /var/www/html
RUN apk add --no-cache git nginx php83 php83-curl php83-fpm php83-mbstring php83-openssl php83-phar \
  && adduser -D ipp \
  && git clone https://github.com/c0m4r/ip-info-page.git . \
  && rm -rfv .git .github .gitignore docker-compose.yml
  && chown -R ipp:ipp /var/www/html \
  && mv .docker/nginx/conf.d/default.conf /etc/nginx/http.d/default.conf \
  && sed -i 's/php-fpm/127.0.0.1/g;' /etc/nginx/http.d/default.conf
  && apk del --no-cache git \
  && rm -rfv .docker
  && wget https://getcomposer.org/download/2.6.6/composer.phar \
  && echo "72600201c73c7c4b218f1c0511b36d8537963e36aafa244757f52309f885b314 composer.phar" | sha256sum -c || rm composer.phar \
  && php83 composer.phar update
EXPOSE 80
CMD [ "/bin/sh", "-c", "nginx && php-fpm83 -F" ]
