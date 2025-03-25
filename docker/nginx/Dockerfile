FROM alpine:3.20

SHELL ["/bin/ash", "-eo", "pipefail", "-c"]

RUN set -eux; \
    apk add --no-cache tzdata nginx nginx-mod-http-brotli; \
    rm -rf /var/cache/apk/; \
    rm -rf /root/.cache; \
    rm -rf /tmp/*; \
    cp /usr/share/zoneinfo/Europe/Moscow /etc/localtime; \
    echo "Europe/Moscow" >> /etc/timezone; \
    mkdir /var/cache/nginx; \
    chown nginx:nginx /var/cache/nginx;

USER nginx

COPY ./docker/nginx/config/ /etc/nginx/

WORKDIR /var/www/html

COPY --chown=nginx:nginx --from=php_builder /var/www/html/public ./public
COPY --chown=nginx:nginx --from=node_builder /app/public/build ./public/build

# https://docs.docker.com/reference/dockerfile/#healthcheck
HEALTHCHECK --interval=5s --timeout=2s \
  CMD nc -vz 127.0.0.1 8080 && echo 'true'  || exit 1

EXPOSE 8080

ENTRYPOINT ["nginx"]

CMD ["-g", "daemon off;"]
