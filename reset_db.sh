# !/bin/bash <= shebang pour dire sous quel environnement exécuter le script

# Suppression et recréation de la bdd
echo "Suppression de la base de données..."
symfony console doctrine:database:drop --force
echo "Création de la base de données..."
symfony console doctrine:database:create

# Suppression des fichiers dans dossier migrations
echo "Suppression des fichiers de migration..."
rm -rf migrations/*

# Création d'une nouvelle migration
echo "Création d'une nouvelle migration..."
symfony console make:migration

# Exécution de la migration, --no-interaction pour ne pas demander confirmation
echo "Exécution de la migration..."
symfony console doctrine:migrations:migrate --no-interaction

# Chargement des fixtures
echo "Chargement des fixtures..."
symfony console doctrine:fixtures:load --no-interaction

echo "Base de données réinitialisée et données de test chargées avec succès."
