FROM php:8.2-apache

# Instala extensão pdo_mysql
RUN docker-php-ext-install pdo pdo_mysql

# Habilita Apache mod_rewrite (se precisar roteamento)
RUN a2enmod rewrite

# Copia app
WORKDIR /var/www/html
COPY public/ ./          # raiz pública
COPY api/ ./api/         # endpoint /api/mensagem

# Porta padrão do Apache
EXPOSE 80

# Healthcheck
HEALTHCHECK --interval=30s --timeout=3s CMD wget -qO- http://localhost/ || exit 1
