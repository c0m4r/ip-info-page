# check=error=true

# Base images are pinned by tag *and* digest; Dependabot bumps both together.
FROM composer:2.11.1@sha256:8de2184c6de224f4689e8f80ed5f5edaf0030b6b8b7839a22a2bcb6722ac87d0 AS composer

# Build stage: install exactly what composer.lock pins (plugins are disabled in composer.json)
FROM php:8.5.13-fpm-alpine3.24@sha256:51414d5291758fe27f58a64c7e8a078465d32cc7dcef7bda803f5b36019802e0 AS vendor
COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /build
COPY composer.json composer.lock ./
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --no-interaction --no-progress --prefer-dist --optimize-autoloader

# Runtime stage: nginx + PHP-FPM, both running as the unprivileged www-data user
FROM php:8.5.13-fpm-alpine3.24@sha256:51414d5291758fe27f58a64c7e8a078465d32cc7dcef7bda803f5b36019802e0
RUN apk add --no-cache nginx~1.30 \
  && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY .docker/php/zz-ipp.ini "$PHP_INI_DIR/conf.d/"
COPY .docker/php/zz-ipp-fpm.conf /usr/local/etc/php-fpm.d/
COPY .docker/nginx/nginx.conf /etc/nginx/nginx.conf
WORKDIR /var/www/html
# Application files stay owned by root, so the runtime user cannot modify them
COPY --from=vendor /build/vendor vendor
COPY index.php config.php manifest.json favicon.ico icon.png icon-144.png icon-512.png ./
COPY css css
COPY js js
COPY tpl tpl
USER www-data
EXPOSE 8080
HEALTHCHECK --interval=60s --timeout=5s --start-period=10s CMD ["wget", "-q", "-O", "/dev/null", "http://127.0.0.1:8080/"]
# PHP-FPM first, so nginx never answers with 502 while PHP-FPM is still starting
CMD ["/bin/sh", "-c", "php-fpm --daemonize && exec nginx -e stderr"]
