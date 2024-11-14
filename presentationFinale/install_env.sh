#!/bin/bash

# Clone du projet
echo "Clonage du dépôt Git"
git clone https://github.com/leotexier348/sortir.com.git
cd sortir.com

# Installation des dépendances PHP avec Composer
echo "Installation des dépendances"
composer install

# Initialisation de Tailwind CSS
echo "Initialisation de Tailwind CSS"
composer require symfonycasts/tailwind-bundle
php bin/console tailwind:init
php bin/console tailwind:build
echo "Tailwind CSS est configuré"

# Suppression de la base de données
echo "Suppression de la base de données si elle existe..."
symfony console doctrine:database:drop --force

# Création de la base de données
echo "Création de la base de données..."
symfony console doctrine:database:create

# Génération et exécution des migrations
echo "Exécution des migrations..."
symfony console make:migration
symfony console doctrine:migrations:migrate --no-interaction
echo "Base de données créée et migrations appliquées"

# Chargement des fixtures
echo "Chargement des fixtures..."
php bin/console doctrine:fixtures:load --no-interaction
echo "Fixtures chargées"

# Nettoyage et compilation du cache et des assets
echo "Nettoyage du cache et compilation des assets..."
symfony console cache:clear
symfony console asset:install
symfony console asset:compile

echo "Passage en environnement de production (prod)"
sed -i 's/^APP_ENV=.*/APP_ENV=prod/' .env

echo "Initialisation terminée avec succès. Bravo !"
echo "Initialisation terminée avec succès. Bravo !"
