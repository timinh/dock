# dock

Permet de générer rapidement une structure de projet Php (Avec ou sans Docker).

### Commandes : 
- php dock.phar add:container => Ajoute un container (choix multiples)
- php dock.phar add:ci        => Permet de choisir la version de Php pour générer un pipeline gitlab CI prédéfini
- php dock.phar add:makefile  => Génère un Makefile avec les commandes sélectionnées (Pour docker, symfony et nodejs)

### Installation : 
Récupérer l'application phar en faisant : 
  wget https://github.com/timinh/dock/tree/master/dist/dock.phar

Puis utiliser les commandes ci-dessus.

### Installation pour le développement : 
 - Cloner le dépot,
 - Installer les dépendances via `composer install`
