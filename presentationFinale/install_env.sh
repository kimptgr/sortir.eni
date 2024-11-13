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
echo "bundle tailwind telechargé"
php bin/console tailwind:init
echo "tailwind fonctionne"

echo "Initialisation terminée avec succès. Bravo"
