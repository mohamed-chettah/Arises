# Utiliser l'image PHP 8.2 basée sur Alpine
FROM php:8.2-fpm-alpine

# Installer les dépendances requises, outils de compilation et extensions PHP
RUN apk add --no-cache \
    git \
    unzip \
    sqlite-dev \
    libzip-dev \
    zip \
    build-base \
    linux-headers \
    && docker-php-ext-install pdo pdo_sqlite zip bcmath sockets \
    && apk del build-base linux-headers

# Copier Composer depuis son image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www

# Copier les fichiers du projet
COPY . .

# Installer les dépendances du projet avec Composer
RUN composer install --no-interaction --optimize-autoloader

# Donner les permissions nécessaires
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Exposer le port
EXPOSE 5005

# Commande par défaut pour démarrer le serveur
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=5005"]
