#!/bin/bash

# Clonage du dépôt
echo "Clonage du dépôt Git"
git clone https://github.com/kimptgr/sortir.eni.git
cd sortir.eni

# Construction des conteneurs Docker
echo "Construction des conteneurs Docker"
docker compose up --build -d

# Installation des dépendances PHP avec Composer
echo "Installation des dépendances"
docker exec -it sortir_app composer install

# Initialisation de Tailwind CSS
echo "Initialisation de Tailwind CSS"
docker exec -it sortir_app php bin/console tailwind:init
docker exec -it sortir_app php bin/console tailwind:build
echo "Tailwind CSS est configuré"

# Suppression de la base de données si elle existe
echo "Suppression de la base de données si elle existe..."
docker exec -it sortir_app php bin/console doctrine:database:drop --force || true

# Création de la base de données
echo "Création de la base de données..."
docker exec -it sortir_app php bin/console doctrine:database:create

# Génération et exécution des migrations
echo "Exécution des migrations..."
docker exec -it sortir_app php bin/console make:migration || true
docker exec -it sortir_app php bin/console doctrine:migrations:migrate --no-interaction
echo "Base de données créée et migrations appliquées"

# Chargement des fixtures
echo "Chargement des fixtures..."
docker exec -it sortir_app php bin/console doctrine:fixtures:load --no-interaction
echo "Fixtures chargées"

# Nettoyage et compilation du cache et des assets
echo "Nettoyage du cache et compilation des assets..."
docker exec -it sortir_app php bin/console asset:install
docker exec -it sortir_app php bin/console asset:compile

echo "L'application Symfony est prête à être utilisée !"

echo "Passage en environnement de production (prod)"
sed -i 's/^APP_ENV=.*/APP_ENV=prod/' .env

docker exec -it sortireni-sortir_app-1 php bin/console cache:clear --env=prod
docker exec -it sortireni-sortir_app-1 php bin/console cache:warmup --env=prod

echo "Initialisation terminée avec succès. Bravo !"
