Participants : Wilhem HO et Nathan Bouche

Lien du dépôt Github : https://github.com/HoWilhem/Symfony_B3IN.git

Conditions d'installations du projet :

Cloner le depot : git clone https://github.com/HoWilhem/Symfony_B3IN.git

1. Installer les bonnes versions :

- Installer Symphony version 6.2
- Installer PHP version 8.2
- Installer Composer

2. Créer la base de données et la configurer dans le fichier .env avec la commande : php bin/console doctrine:database:create

3. Dans la bdd, on a crée les différentes entités ( doctrines) : User, Book, Category, Author ou encore Emprunt avec la commande : php bin/console doctrine:schema:update --force

4. Ensuite on implémente les routes CRUD donc ADD, UPDATE, DELETE pour les users et les livres

Exemples :

- /livres/add pour ajouter un book
- /livres/list pour afficher tous les livres
- /livres/update/id/titres pour modifier un livre
- /livres/delete/id pour supprimer un livre

5. Utilisation de Postman pour tester le backend et les routes en utilisant les requêtes
