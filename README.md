Ositel Symfony3 test
========================

### Bundles/Outils ajoutés:
- **symfony/flex** : la nouvelle plugin Symfony pour la gestion des dépendances  
- **symfony/dotenv** : pour la gestion des paramètres via les variables d'environnement dans le fichier .env au lieu de parametres.yml.
- **api-platform/api-pack** : pai_platform pour la partie API.
- **sonata-project/admin-bundle** et **sonata-project/doctrine-orm-admin-bundle** : pour l'administration du projet
- **symfony/maker-bundle** : pour la génération des Entities, CRUD ...
- **doctrine/doctrine-fixtures-bundle** : pour la génération des données de tests (commande `php bin/console doctrine:fixtures:load -n` ou bien `php bin/console doctrine:fixtures:load -n --purge-with-truncate --env=test` pour l'environment de test)
- **sebastian/phpcpd** : pour la détection de code dupliqué pour le refactoring du code (commande `vendor/bin/phpcpd src` pour détecter le code dupliqué et appliquer le refactoring manuellement)
- **friendsofphp/php-cs-fixer** : Pour la validation/Amélioration de la qualité du code (Fichier config ".php-cs",  commandes `vendor/bin/php-cs-fixer fix --diff --dry-run` pour valider la qualité du code et afficher les modifications nécessaires et `vendor/bin/php-cs-fixer fix` pour appliquer les corrections sur le code)

### Instalaltion du projet:
- Copier le fichier .inv.dist vars .env et modifier les paramètres
- `composer install` pour l'installation des dépondances
- `php bin/console do:da:cr` et `php bin/console do:da:cr --env=test` : pour la creation de la base de données
- `php bin/console do:sc:up -f` et `php bin/console do:sc:up -f --env=test` : pour la mise à jour du schema de la base de données
- `php bin/console doctrine:fixtures:load -n` et `php bin/console doctrine:fixtures:load -n --purge-with-truncate --env=test` pour la génération des données de test
## Urls:
- url_du_projet/admin => l'administration du projet créé avec SonataAdminBundle
- url_du_projet/api => la partie API créé avec api platform 
