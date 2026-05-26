FROM php:8.2-cli-alpine

# Instalar extensões PDO para PostgreSQL e MySQL
RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql

WORKDIR /app
COPY . .

# Render define a variável PORT automaticamente
CMD sh -c "php -S 0.0.0.0:${PORT:-10000} router.php"
