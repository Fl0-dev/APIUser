# Feuille de route

#### Développement du projet :
    - Parcours de la documentation de CodeIgniter 3.
    - Installation du repository CodeIgniter 3
    - Création de la base de données `user_api`
    - Création de la table `users`
    - Création du model UserModel
    - Création du controller User
    - Création du controller Admin
    - Création des routes demandées
    - Création des méthodes pour chaque route dans le controller
    - Création des méthodes pour chaque route dans le model
    - Tests via Postman
    - Création des commandes batchs
    - Création de la collection de Postman
    - Documentation (Postman et Readme.md)
    - LogBook

#### Temps de développement :
    - Conception de l'api : 2h
    - Initialisation de l'api (installation/base de données/config des librairies) : 3h
    - Développement des routes (controller/model avec méthodes et tests dans Postman) : 10h
    - Développement des batchs : 1h
    - Rédaction de la collection de Postman : 1h
    - Documentation : 1h
    - LogBook : 2h

#### Réflexion sur le fonctionnement de l'api et configuration  
* CodeIgniter 3 étant très limité pour ce genre de projet, j'ai regardé pour avoir des requests en POST, PUT, DELETE, etc. J'ai trouvé une solution qui consiste à utiliser une librairie externe, **REST_Controller** et **Format**

*  Sur la sécurité et les variables d'environnement, j'ai commencé à regarder comment sécuriser l'api et comment utiliser les variables d'environnement. J'ai trouvé une librairie **vlucas/phpdotenv** qui permet de gérer des .env, j'ai donc installé cette librairie et commencé à l'utiliser en créant un fichier .env et en y mettant les variables d'environnement.

* Sur la construction des routes et les autorisations :
Je suis parti du principe que le BackOffice et le FrontOffice de l'api seront protéger par un Bearer Token spécifique à chaque. Chaque route est ainsi vérifiée. Je l'ai fait directement dans le controller pour aller plus vite, mais la meilleure méthode serait de créer un middleware pour vérifier les tokens.

Côté FrontOffice, un user peut s'inscrire, se connecter, modifier ses données, se déconnecter.
Côté BackOffice, un admin peut s'inscrire, peut voir tous les utilisateurs, les supprimer selon leur id ou leur email.

#### Choix de conception
J'ai rajouté un champ password pour que côté FO, un user se connecter et puisse modifier que ses données.

Le champ "status professionnel" est un booléen : "is_admin". Il est à 0 par défaut, Il n'y a que les utilisateurs qui s'enregistre via le BackOffice qui sont admin.

Je suis parti du principe que le login soit juste une vérification pour le front, ainsi sur la route spécifique, j'indique que l'utilisateur est bien vérifié et je lui renvoie les données de l'utilisateur à même d'être modifieés sans le password. Le logout est géré côté front.

Il n'y a pas besoin d'être connecté côté BO, un bearer token suffit pour les requêtes.

Pour la commande batch demandé, j'ai créé une commande qui permettrai d'être lancer par une tache CRON tous les jours à 00h00 par exemple.

J'ai aussi rajouté une commande qui permet de remplir la base de données d'utilisateurs via Faker.

J'ai aussi créer une collection de Postman pour tester l'application même si elle est très succinte niveau documentation.

#### Réussites
Je suis parti de loin et je suis content d'avoir réussi à faire une api fonctionnelle avec CodeIgniter 3. Je ne connaissais pas la version 3 et je n'ai codé que sur la version 4 et que depuis peu.
J'ai réussi à faire toutes les routes demandées et à les tester via Postman. J'ai aussi réussi à faire des commandes batchs pour remplir la base de données et pour supprimer les utilisateurs inactifs.
J'avoue que j'ai eu du mal à me dépatouiller des routes et des controllers avec REST_Controller mais j'ai réussi à tout faire fonctionner.
J'utilise le plus souvent des frameworks plus documentés et complets, j'ai donc appris à me débrouiller avec ce que j'avais et Internet tout le long du projet.

#### Difficultés rencontrées
* Je reste sceptique sur mes choix de conception et je pense que je pourrais faire mieux.
* La documentation de CodeIgniter 3 est très limitée, j'ai donc du chercher des solutions sur internet pour chaque problème rencontré.
* J'ai passer trop de temps sur la liaison entre le controller et les routes ainsi que sur la validation des données.
* Par manque de temps et de souci de config (.env inexistant pour phpunit) je n'ai pas pu faire de tests.

#### Améliorations possibles
* Créer un middleware pour vérifier les tokens, je vérifie directement dans le controller à chaque fois.
* La mise en place de tests unitaires et fonctionnels. J'ai essayé de mettre en place phpunit mais je n'ai pas réussi à le faire fonctionner avec les variables d'environnement. Je m'y suis mis à la fin du projet et je n'ai pas eu le temps de le faire fonctionner.
* APIPlatform est, je pense, une solution plus complète pour ce genre de projet (même si peu de routes sont demandées ici). J'aurais préférer l'utiliser pour mieux gérer les routes et les autorisations.
* La gestion des erreurs est très basique, je pourrais rajouter des messages d'erreurs plus précis.
* La documentation est très succinte, je pourrais rajouter des exemples de requêtes et de réponses. Je voulais utiliser Swagger mais je n'ai pas eu le temps de le faire.

#### Ressenti personnel
Mon poste actuel me fait passer de front à back quasi quotidiennement et ça m'a fait plaisir de revenir en php juste sur du back. 
Je pense que j'aurai pu aller beaucoup plus loin avec plus de temps et de documentation et je reste assez frustré de ne pas avoir pu faire de tests unitaires et fonctionnels ansi que de ne pas avoir pu utiliser Swagger pour la documentation.
J'ai fait pas mal de concessions pour ne pas avoir à livrer un projet non fonctionnel.
J'aurai tellement voulu utilisé APIPlatform pour ce projet mais c'était un bon exercice et j'ai appris pas mal de petites choses.

J'espère que ce projet plaira malgré ses failles et je serais ravi de pouvoir discuter sur les améliorations possibles ou les sur les choix de conception.
