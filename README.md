# Blossom-Buddy

## Introduction
Bienvenue dans ce TP ! Nous allons réaliser ensemble le backend d'une application qui vous permettra de savoir quand arroser vos plantes 🌱. En utilisant le framework Laravel 11, vous allez apprendre à créer une API backend complète, gérer l'authentification des utilisateurs, manipuler des données provenant d'API externes, et bien plus encore. Ce projet vous aidera à appliquer et à approfondir vos connaissances en développement web tout en découvrant de nouvelles pratiques et concepts essentiels.

## Objectifs Pédagogiques
À l'issue de ce TP, vous serez capables de :

1. Configurer et utiliser Laravel 11 pour créer une application backend. 🛠️
2. Implémenter l'authentification et l'inscription des utilisateurs avec Sanctum. 🔐
3. Créer et manipuler des modèles et des migrations pour gérer les données de l'application. 🗄️
4. Définir des routes et des contrôleurs pour structurer et gérer les fonctionnalités de l'application. 🛤️
5. Intégrer des API externes pour enrichir les données de votre application. 🌐
6. Mettre en cache des données pour optimiser les performances et réduire les appels API inutiles. ⚡
7. Produire du code métier pour encapsuler la logique métier de l'application. 🧩
8. Appliquer les principes SOLID pour écrire un code propre, modulaire et maintenable. 📏
9. Refactoriser et améliorer le code existant en fonction des bonnes pratiques de développement. 🔄

J'espère que ce projet vous inspirera et vous permettra de développer des compétences précieuses en développement web, tout en créant une application utile et originale. L'idée est aussi de réaliser un backend typique d'un projet présentable pour la certification CDA !!

## API Endpoint
Dans ce TP, nous nous concentrerons uniquement sur le développement du backend de notre application. 🚀 Nous ne créerons pas de front-end pour cette application. Afin de tester les différentes routes de notre API, nous utiliserons des outils comme Postman ou Insomnia.

Ces outils vous permettront de :

* Envoyer des requêtes HTTP (GET, POST, PUT, DELETE) à votre API. 📨
* Voir les réponses de votre API et inspecter les données retournées. 🔍
* Tester les différentes fonctionnalités de votre application, comme l'authentification, la gestion des plantes et la récupération des données de l'API externe. ✅

Assurez-vous d'installer l'un de ces outils sur votre machine avant de commencer. Voici les liens pour les télécharger :

- [Postman](https://www.postman.com/)
- [Insomnia](https://insomnia.rest/)

Utiliser ces outils vous permettra de valider le bon fonctionnement de votre API à chaque étape du développement. Bonne découverte et bons tests ! 🌟

## À propos de Laravel
Laravel est un framework PHP open-source qui vise à rendre le développement web à la fois simple et agréable. Connu pour sa syntaxe élégante et son riche écosystème, Laravel facilite la création d'applications web robustes grâce à ses nombreuses fonctionnalités intégrées, telles que le routage, la gestion de base de données via Eloquent ORM, l'authentification, les tests unitaires, et bien plus encore.

Laravel se distingue par sa capacité à accélérer le développement en offrant une structure claire et des outils puissants pour les tâches courantes, ce qui permet aux développeurs de se concentrer sur la logique métier plutôt que sur les détails techniques.

Si vous n'êtes pas encore à l'aise avec Laravel ou si vous souhaitez renforcer vos compétences, nous vous recommandons vivement de suivre le [Bootcamp Laravel-Blade](https://bootcamp.laravel.com/). Ce bootcamp vous guidera à travers les bases de Laravel et vous préparera à aborder ce TP avec confiance. Profitez de cette ressource gratuite pour vous familiariser avec les concepts et les outils essentiels de Laravel avant de commencer ce projet.

## Importance du Type Hinting et des Interfaces

Dans ce TP, nous allons appliquer des concepts de programmation avancés pour garantir un code de haute qualité. Deux de ces concepts essentiels sont le type hinting et l'utilisation des interfaces. Ces pratiques sont cruciales pour plusieurs raisons :

### Type Hinting

Le type hinting consiste à spécifier les types des arguments et des valeurs de retour des fonctions et méthodes. Voici pourquoi c'est important :

1. **Clarté du Code** : En spécifiant les types, vous rendez votre code plus lisible et compréhensible. Les développeurs peuvent facilement comprendre quelles sortes de valeurs sont attendues et retournées par les méthodes.
```php
public function store(Request $request): JsonResponse
```

2. **Détection Précoce des Erreurs** : Le typage strict permet de détecter les erreurs de type lors du développement plutôt que de les découvrir lors de l'exécution, réduisant ainsi les bugs.

3. **Auto-complétion** : Les IDE modernes utilisent les types pour fournir des suggestions de code, ce qui améliore la productivité et la précision des développeurs.

Ressource : [Mastering Type Hinting in PHP/Laravel: A Comprehensive Guide](https://medium.com/@aiman.asfia/mastering-type-hinting-in-laravel-a-comprehensive-guide-396e37e9d119)

### Interfaces

Les interfaces définissent des contrats que les classes doivent respecter. Voici pourquoi les interfaces sont essentielles :

1. **Flexibilité et Extensibilité** : En utilisant des interfaces, vous pouvez changer les implémentations sans modifier le code qui dépend de ces interfaces. Cela facilite l'extension et la modification du comportement des classes sans affecter le reste du système.
```php
interface PlantServiceInterface
{
    public function fetchAndStoreAllPlants(): void;
}
```

2. **Testabilité** : Les interfaces permettent de créer des implémentations factices ou des mocks pour les tests, ce qui rend votre code plus testable.

3. **Respect des Principes SOLID** : Les interfaces aident à adhérer au principe de responsabilité unique (Single Responsibility Principle) et au principe de l'inversion de dépendance (Dependency Inversion Principle), rendant le code plus modulaire et maintenable.

Ressource : [What is Interface and How to use it in Laravel](https://dev.to/snehalkadwe/what-is-interface-and-how-to-use-it-in-laravel-4gin). 

⚠️ Avec Laravel 11 vous avez la commande [php artisan make:interface](https://laraveldaily.com/post/laravel-11-artisan-make-interface-command).


## Initialisation du Projet et Gestion des Utilisateurs

### Installation et Configuration de Laravel 11

Tout d'abord veuillez installer un nouveau projet Laravel [https://laravel.com/docs/11.x#creating-a-laravel-project](https://laravel.com/docs/11.x#creating-a-laravel-project), que vous appelerez blossom-buddy.

Configurez le fichier .env pour définir les paramètres de connexion à la base de données et créer la BDD blossom_buddy

Pour intercepter les envois de mails pendant le développement, nous utiliserons [Mailtrap](https://mailtrap.io/). Suivez ces étapes :

1. Créer un Compte Mailtrap
2. Configurer Mailtrap dans Laravel
    - Dans votre tableau de bord Mailtrap, créez une inbox et récupérez les informations de configuration SMTP.
    - Mettez à jour votre fichier .env avec les informations SMTP de Mailtrap

### Gestion des Utilisateurs avec Sanctum
Avec l'aide de la documentation : [https://laravel.com/docs/11.x/sanctum](https://laravel.com/docs/11.x/sanctum), **Installer** et **Configurer** Sanctum.

Créez maintenant un fichier api.php dans le dossier routes de Laravel, puis dans ce même dossier supprimez le fichier web.php (ce fichier est utilisé si vous voulez utiliser votre Laravel comme une web app, ce qui n'est pas notre cas ici). Ensuite dans le fichier bootstrap/app.php, remplacez la référence à web.php par api.php en adaptant le chemin :

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

```

Dans le fichier api.php, vous allez définir les routes de votre api. Profitez-en pour définir 2 routes, une pour l'inscription et une pour l'authentification d'un utilisateur.

Générez maintenant le controlleur responsable de l'authentification : "AuthController".

#### Fonctionnement de l'Authentification

Pour mieux comprendre le processus d'authentification, voici une explication détaillée :

1. Inscription de l'utilisateur
    - L'utilisateur envoie une requête POST à l'endpoint /register avec ses informations (prénom, nom, email, mot de passe).
    - Le serveur valide les informations, crée un nouvel utilisateur dans la base de données et génère un token d'authentification.
    - Le token est renvoyé à l'utilisateur, ce qui lui permet de s'authentifier pour les futures requêtes.

2. Connexion de l'utilisateur
    - L'utilisateur envoie une requête POST à l'endpoint /login avec ses identifiants (email, mot de passe).
    - Le serveur vérifie les informations d'identification :
        - Si les informations sont correctes, un token d'authentification est généré et renvoyé à l'utilisateur.
        - Si les informations sont incorrectes, une réponse d'erreur est renvoyée.

3. Utilisation du token
    - Pour accéder aux endpoints protégés de l'API, l'utilisateur doit inclure le token d'authentification dans l'en-tête de la requête HTTP.
    - Le serveur valide le token avant d'exécuter la requête, garantissant que seul un utilisateur authentifié peut accéder aux ressources protégées.

#### Exemple de Requête d'Authentification

1. Inscription (Register)
    - Endpoint : /register
    - Méthode : POST
    - Corps de la requête :
    ```json
    {
        "name": "John Doe",
        "email": "john.doe@example.com",
        "password": "password123",
        "password_confirmation": "password123"
    }
    ```
    - Réponse :
    ```json
    {
        "access_token": "token_string",
        "token_type": "Bearer"
    }
    ```
2. Connexion (Login)
    - Endpoint : /login
    - Méthode : POST
    - Corps de la requête :
    ```json
    {
        "email": "john.doe@example.com",
        "password": "password123"
    }
    ```
    - Réponse :
    ```json
    {
        "access_token": "token_string",
        "token_type": "Bearer"
    }
    ```

Avec cette structure, l'authentification dans votre projet Laravel est mise en place, et vous pouvez sécuriser vos endpoints en exigeant que les utilisateurs incluent le token dans leurs requêtes. 🚀🔒

Vous utiliserez par la suite sanctum en tant que middleware pour forcer la vérification de l'auth sur les routes qui la necessite [https://laravel.com/docs/11.x/sanctum#token-ability-middleware](https://laravel.com/docs/11.x/sanctum#token-ability-middleware)


## Gestion des Plantes

### Création des Modèles et des Migrations pour les Plantes

Générez un modèle Plant avec migration associée puis définissez les colonnes de la table des Plantes : 
Pour l'instant,
- id
- common_name
- watering_general_benchmark, qui sera un tableau qui ressemblera à 
    ```json
    {
        "value": "5-7",
        "unit": "days"
    }
    ```

Dans cette même migration, il va falloir représenter la relation ManyToMany entre User et Plant, pour ça vous allez créer une table pivot user_plant, qui contiendra son propre id et les id de la plant et du user, où chaque id est une clé étrangère des tables Plant et User.

N'oubliez pas d'executer les migrations.
Tant que les migrations ne passe pas, videz la base de donnée, corriger vos migrations et ré-executez les.

### Création des Routes et des Contrôleurs pour les Plantes

Dans routes/api.php, définissez les routes suivantes :

1. Récuperer les informations de toutes les plantes
    - Endpoint : /plant
    - Méthode : GET
    - Authentification : non requise
    - Principe : Donne toutes les plants.

2. Ajouter une plante
    - Endpoint : /plant
    - Méthode : POST
    - Authentification : non requise
    - Principe : Permet d'ajouter une plante (il ne s'agit pas ici des plantes possédé par l'utilisateur, mais de simplement ajouter une plante en base de donnée).


3. Récuperer les informations d'une plante
    - Endpoint : /plant/{name} (utilisez le nom Anglais indiqué dans common_name pour le moment)
    - Méthode : GET
    - Authentification : non requise
    - Principe : l'utilisateur entre le nom d'une plante dans un champ de recherche, le backend reçoit le nom de la plante dans l'url, puis utilise Eloquent pour récupérer la plante et ses informations. Il faut ici retourner un JSON contenant les informations de la plante, ou une erreur adaptée en fonction du cas rencontré (il est important sur une api d'avoir une gestion d'erreur propre pour TOUTE vos routes).

4. Supprimer une plante
    - Endpoint : /plant/{id}
    - Méthode : DELETE
    - Authentification : non requise
    - Principe : Permet de supprimer une plante de la base de donnée à partir de son id.

5. Permettre à un utilisateur d'entrer la plante qu'il a et l'endroit où il est
    - Endpoint : /user/plant
    - Méthode : POST
    - Authentification : **requise** (utiliser le middleware auth:sanctum)
    - Principe : l'utilisateur entre le nom d'une plante dans le formulaire, la ville dans laquelle il est, et le front-end nous envoie ces informations ainsi que le token d'auth de l'utilisateur. Si les deux sont bon on ajoute en base de données, le fait que tel utilisateur possède tel plante. Sinon on retourne des erreurs appropriés.

6. Permet de récupérer toutes les plantes possédé par un utilisateur
    - Endpoint : /user/plants
    - Méthode : GET
    - Authentification : **requise** (utiliser le middleware auth:sanctum)
    - Principe : permet de récupérer toutes les plantes qu'un utilisateur possède pour faire un affichage.

7. Permettre à l'utilisateur de supprimer une plante qu'il a indiqué posséder
    - Endpoint : /user/plant/{id}
    - Méthode : DELETE
    - Authentification : **requise** (utiliser le middleware auth:sanctum)
    - Principe : l'utilisateur supprime une plante de la liste des plantes qu'il possède. On reçoit l'id de la relation Plant - User et le token de l'utilisateur.

Une fois ces routes crée, il faut faire les controlleurs pour les plantes "PlantController", et pour la gestion des plantes via les actions de l'utilisateur : "UserPlantController" puis **Implémentez** les méthodes nécessaires sur chacun des routes.

## Documentation de l'API avec Swagger

### Pourquoi Documenter votre API ?

📚 La documentation d'une API est essentielle pour plusieurs raisons :

1. **Faciliter la Compréhension** : Une bonne documentation permet aux développeurs de comprendre facilement comment utiliser l'API, quelles sont les routes disponibles, les paramètres requis et les réponses attendues.

2. **Améliorer la Collaboration** : Lorsque plusieurs développeurs travaillent sur un même projet, une documentation claire et concise facilite la collaboration et réduit les risques de malentendus.

3. **Assurer la Maintenabilité** : Une API bien documentée est plus facile à maintenir et à mettre à jour. Les nouvelles fonctionnalités ou modifications peuvent être rapidement comprises et intégrées.

4. **Améliorer l'Expérience Utilisateur** : Les utilisateurs de votre API (qu'ils soient internes ou externes) auront une meilleure expérience si ils peuvent facilement trouver comment interagir avec vos services.

Pour documenter votre API, nous allons utiliser Swagger. Swagger est un ensemble d'outils qui vous aide à concevoir, construire, documenter et consommer des services Web RESTful.

### Mise en Place de Swagger avec Laravel

Utilisez les packages [swagger-php](https://github.com/zircote/swagger-php) et [L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger) pour mettre en place Swagger sur Laravel.

Je vous mets ici un Medium qui trait du sujet : [Set up Laravel with Swagger for comprehensive API documentation. Step-by-step instructions](https://medium.com/@mark.tabletpc/set-up-laravel-with-swagger-for-comprehensive-api-documentation-step-by-step-instructions-d30552ca8051)

Il faut documenter toutes les routes utilisable, et vous devrez mettre à jour la documentation au fur et à mesure que l'on refactorisera le code, si l'on modifie certaine route !

## Gestion du Caching API

Dans cette section, nous allons intégrer des API externes pour obtenir des informations sur les plantes et les conditions météorologiques. Comme notre application dépend fortement de ces données externes, il est crucial d'optimiser les performances et la fiabilité en utilisant une stratégie de caching.

Le caching consiste à stocker temporairement les réponses des requêtes API pour éviter de les refaire à chaque demande. Cette pratique courante permet de :

* Améliorer les Performances : En réduisant le nombre de requêtes externes, on diminue le temps de réponse de l'application.
* Réduire les Coûts : Minimiser les appels API externes peut réduire les coûts associés à ces services, surtout si des frais sont appliqués par requête.
* Augmenter la Fiabilité : Le cache peut servir de source de données de secours en cas de défaillance temporaire de l'API externe.

Nous allons voir comment mettre en place cette stratégie pour l'API des plantes et l'API météo.

### Intégration de l'API des Plantes

Pour obtenir des informations détaillées sur les plantes, nous allons intégrer une API externe spécialisée dans les données botaniques. Plutôt que de faire des requêtes à chaque fois que nous avons besoin de ces informations, nous allons adopter une stratégie de caching plus efficace. 

Cette stratégie consistera à récupérer les données depuis l'API, à les remanier pour ne garder que les informations nécessaires, puis à les sauvegarder dans notre base de données. Ainsi, nous pourrons accéder rapidement aux données des plantes sans dépendre continuellement de l'API externe, ce qui améliorera les performances de notre application et réduira les coûts liés aux appels API. 🌿

#### Configuration de l'API des Plantes

Nous choisirons l'API [https://perenual.com/](https://perenual.com/) car elle fournit les informations dont on aura besoin. Créez-vous un compte, récuperer votre clé API, consultez la documentation, testez quelques routes avec Postman ou Insomnia, pour comprendre comment l'API fonctionne, et voir quels informations vous pouvez en tirer.

Une fois que vous aurez pris en main l'API, modifiez votre Model Plant et mettez à jour les colonnes de la base de données associée, par le biais d'une migration. L'objectif ici est de modifier notre Model pour que l'on puisse récupérer un maximum d'informations intéressantes, donc regardez sur l'API et reperez les données qui vous semble intéressantes pour nos futurs fonctionnalités.

#### Création d'un Service pour l'API des Plantes

Créez un service dédié et son interface pour interagir avec l'API des plantes. Ce service sera responsable de faire les **requêtes API**, de **filtrer les données** nécessaires et de les **stocker** dans la base de données.

Je vous mets encore une fois un Medium qui vous permettra de comprendre la marche à suivre : [Understanding Laravel Service Classes: A Comprehensive Guide](https://medium.com/@laravelprotips/understanding-laravel-service-classes-a-comprehensive-guide-1f22310c70bd)


Quant à l'utilisation de ce service il existe plusieurs solutions, en voici quelques-unes (de manière non exhaustive) :

* Si les données de l'API changent fréquemment ou si vous avez besoin de disposer de données à jour à tout moment, vous pouvez configurer une tâche planifiée pour appeler régulièrement l'API et mettre à jour votre base de données. Dans ce cas-là il faudrait mettre en place ce que l'on appelle du task scheduling (voir [Task Scheduling](https://laravel.com/docs/11.x/scheduling)), lié à un cron sur le serveur.

* Si l'API externe prend en charge les webhooks, vous pouvez configurer un webhook pour recevoir des notifications chaque fois que les données changent. Cela permet de mettre à jour votre base de données en temps réel, sans avoir besoin de faire des requêtes régulières. Cependant, celà se traduit par des calls API plus ou moins régulier (à chaque mise à jour de l'API).

* Si les données des plantes ne changent pas fréquemment ou si vous préférez contrôler manuellement quand les données sont mises à jour, vous pouvez configurer une route API dédiée pour appeler le service et mettre à jour la base de données.

Nous choisirons la dernière solution proposée. vous créerez une route /plant/update qui appelera une fonction du PlantController que vous créerez pour l'occasion. Cette fonction fera appelle au service de mise à jour de notre base de donnée de plantes.

### Gestion de l'API Météo

Pour notre application Blossom Buddy, nous allons utiliser l'API de [WeatherAPI](https://www.weatherapi.com/) pour obtenir les données météorologiques nécessaires afin de déterminer le meilleur moment pour arroser les plantes. La stratégie de gestion du caching pour l'API météo sera différente de celle utilisée pour l'API des plantes. Voici les étapes à suivre :

1. Lorsqu'un utilisateur ajoute une nouvelle plante, nous vérifierons si nous avons les données météorologiques en cache.

2. Si les données ne sont pas en cache, nous ferons une requête à l'API météo pour obtenir les informations nécessaires.

3. Nous mettrons ensuite en cache ces données pendant 2 heures.

#### Étapes pour Intégrer et Cacher les Données Météo

* Inscrivez-vous sur WeatherAPI et obtenez une clé API. Nous en aurons besoin pour authentifier nos requêtes.

* Comme pour la gestion de l'API des plantes, créez un Service et son interface pour l'API Météo qui sera appelé à chaque fois que l'on aura besoin de la météo, et qui nous enverra soit les données que l'on a en cache, soit des données toute fraîche en fonction du contexte.
Ressource : [Cache](https://laravel.com/docs/11.x/cache#obtaining-a-cache-instance).

* Injecter le service météo dans PlantController et l'utiliser lors de l'ajout d'une plante par un utilisateur (utiliser la ville renseignée dans le formulaire par l'utilisateur dans le service pour donner la météo du lieu concerné).

## Le Prochain Arrosage 🚿

Maintenant que nous avons intégré les données des plantes et les informations météorologiques, nous pouvons calculer et retourner le temps avant le prochain arrosage pour chaque plante. Cette fonctionnalité est cruciale pour notre application **Blossom Budy**, car elle fournit aux utilisateurs des recommandations personnalisées sur l'entretien de leurs plantes.

### Étape pour Calculer le Temps Avant le Prochain Arrosage

1. L'utilisateur entre le nom anglais de la plante qu'il a et la ville dans laquelle il habite.
2. On récupère les données de la plante en question, stockées en BDD.
3. Combiner les informations sur les plantes et la météo pour calculer quand arroser la plante.
4. Enregistrer en base de donnée que tel utilisateur possède la plante.
5. Retourner à l'utilisateur dans combien de temps il devra arroser sa plante.

## Allons plus loin ! 

Maintenant que nous avons mis en place les fonctionnalités de base de notre application, nous pouvons aller encore plus loin pour améliorer l'expérience utilisateur. Une fonctionnalité supplémentaire qui serait extrêmement utile est l'envoi d'un email de rappel à l'utilisateur lorsque le temps avant le prochain arrosage est écoulé. Cela permet à l'utilisateur de recevoir une notification à temps pour arroser ses plantes, sans avoir à vérifier constamment l'application.

Vous avez dû au début de ce TP configurer votre environnement pour utiliser Mailtrap ou un service similaire, nous allons pouvoir configurer une notification Laravel.

### Créer une Notification d'Arrosage 🔔

Le principe est simple, nous allons utiliser les notifications Laravel (voir [Notifications](https://laravel.com/docs/11.x/notifications)), afin de programmer l'envoi de mails.

Créer un fichier de notification WateringReminder qui prendra en paramètre la plante et le temps avant le prochain arrosage, que l'on a calculé précédemment.

Planifiez l'envoi de rappels (voir [Task Scheduling](https://laravel.com/docs/11.x/scheduling)).

## Améliorations possible

* Utilisez les données GPS (latitude, longitude) plutôt que la ville pour être plus précis sur la récupération de la météo.
* Peaufiner l'algorithme de calcul du temps avant le prochain arrosage (ajouter de nouveaux paramètres qui pourraient affecter le temps d'arrosage, comme par exemple si la plante est exposée au soleil ou non...).
* Être capable de traduire la demande de l'utilisateur dans une langue qu'il comprend (par exemple l'utilisateur entre en français le nom d'une plante alors qu'on l'a sauvegardé en BDD en anglais -> il faut que ça marche quand même).


## SOLID vs STUPID

Vous pouvez consultez ce répo github qu traite de la programmation SOLID : [here](https://github.com/G404-CDA/SOLID-vs-STUPID)


Une fois ceci fait, vous allez refactoriser votre code précedemment produit, pour qu'il réponde au mieux aux principes SOLID.
