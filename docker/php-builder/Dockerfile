
FROM php:8.4.1-fpm-alpine3.20

# Install dependecies from APK
RUN set -eux; \
      apk add --no-cache \
        # Healthcheck
        fcgi;

# Install PHP Extensions
COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/
RUN set -eux; \
      install-php-extensions \
        bcmath \
        pcntl \
        pdo_pgsql \
        pgsql \
        xml \
        redis \
        zip

# Clear building cache
RUN set -eux; \
        docker-php-source delete; \
        rm -f /usr/local/bin/install-php-extensions; \
        rm -rf /var/lib/apt/lists/*

COPY --link --chmod=0755 ./docker/php-builder/config/php-healthcheck /usr/local/bin/php-healthcheck

COPY --link ./docker/php-builder/config/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

COPY --link ./docker/php-builder/config/conf.d/zz-custom.ini "${PHP_INI_DIR}/conf.d/zz-custom.ini"

COPY --from=composer/composer:2.8-bin /composer /usr/bin/composer

WORKDIR /var/www/html

USER www-data

COPY --chown=www-data:www-data  . .

RUN set -eux; \
    composer install --no-interaction --no-plugins --no-scripts --optimize-autoloader; \
    composer publish-deploy --no-interaction --no-plugins; \
    # INFO    - DKL-LI-0003: Only put necessary files
    rm -rf /var/www/html/docker; \
    rm -rf /var/www/html/docker-bake.hcl; \
    rm -rf /var/www/html/vendor/laravel/sail/runtimes/8.0/Dockerfile; \
    rm -rf /var/www/html/vendor/laravel/sail/runtimes/8.1/Dockerfile; \
    rm -rf /var/www/html/vendor/laravel/sail/runtimes/8.2/Dockerfile; \
    rm -rf /var/www/html/vendor/laravel/sail/runtimes/8.3/Dockerfile; \
    rm -rf /var/www/html/vendor/laravel/sail/runtimes/8.4/Dockerfile; \
    rm -rf /var/www/html/docker/8.0/Dockerfile; \
    rm -rf /var/www/html/docker/8.1/Dockerfile; \
    rm -rf /var/www/html/docker/8.2/Dockerfile; \
    rm -rf /var/www/html/docker/8.3/Dockerfile; \
    rm -rf /var/www/html/docker/8.4/Dockerfile;
