FROM node:22-alpine

WORKDIR /app

COPY . .

# fix: tightenco/ziggy
COPY --from=php_builder /var/www/html/vendor ./vendor

ENV NODE_ENV=production

RUN set -eux; \
    npm ci; \
    npm run build;


VOLUME ["/app/public/build"]

