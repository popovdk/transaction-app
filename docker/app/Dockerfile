FROM php_builder:latest
WORKDIR /var/www/html
COPY --from=php_builder /var/www/html .
COPY --from=node_builder /app/public/build ./public/build
USER www-data
EXPOSE 9000
CMD ["php-fpm"]
