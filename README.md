# pizzeria_sf6

## Prérequis :
  - Installation de symfony cli.
  - Installation de composer.
  - Un compte Mailtrap pour récupérer les emails (important pour l'inscription sur le site du projet), et inscrire le MAILER_DSN dans le .env.
## Optionnel :
  - Un compte Stripe avec une clé API pour la manipulation du paiement. Ajouter votre clé secrète dans le .env.
---------------------------------------------------------------------------------------------------------------------------------------------

### Après avoir cloner le projet :

  - Lancer les commandes depuis le terminal :
  ```
    composer install
    npm install
  ```
  - Si problème avec des dépendances :
  ```
    npm audit fix --force
  ```
  - Pour importer la DB :
  ```
    symfony console d:d:c
    php bin/console make:migration
    php bin/console d:m:m
  ```
  
  - Si problème avec webpack: 
  ```
    composer require symfony/webpack-encore-bundle
    npm run build
  ```
  
  - Pour générer les fixtures :
  ```
    php bin/console doctrine:fixtures:load
  ```
  
  - Pour démarer le server symfony :
  ```
    symfony server:start
  ```
