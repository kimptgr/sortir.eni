FROM php:8.2-fpm

# Installer les extensions nécessaires
RUN apt-get update && apt-get install -y \
    zip unzip git curl libpq-dev libicu-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql intl opcache

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Installer Node.js pour Tailwind
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Définir le répertoire de travail
WORKDIR /app