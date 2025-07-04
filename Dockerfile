FROM php:8.2-apache

# Instalar extensões do PHP e ferramentas
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite do Apache
RUN a2enmod rewrite

# Copiar aplicação
COPY app/ /var/www/html/

# Permissões
RUN chown -R www-data:www-data /var/www/html