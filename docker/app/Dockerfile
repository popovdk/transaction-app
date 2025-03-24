FROM app_builder:latest
WORKDIR /var/www/html
COPY --from=app_builder /var/www/html .
USER www-data
EXPOSE 9000
CMD ["php-fpm"]
