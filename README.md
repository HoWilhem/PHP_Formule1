Participants : Wilhem HO et Nathan Bouche

1. Installer les bonnes versions :

- Installer Symphony version 6.2
- Installer PHP version 8.2
- Installer Composer

2. Créer la base de données avec les commandes :
   php bin/console doctrine:database:create
   php bin/console doctrine:schema:update --force
   
3.Lancer la fixture:
   php bin/console doctrine:fixtures:load -n
