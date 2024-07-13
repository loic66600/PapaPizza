Papa Pizza - PHP Orienté Objet

🍕 À propos du projet
Ce projet a été développé pour créer une application web de commande de pizzas en ligne. Il s'agit d'une application PHP orientée objet inspirée du fonctionnement d'une pizzeria. L'objectif principal était de créer une plateforme conviviale permettant aux clients de commander leurs pizzas préférées en quelques clics.

🎯 Objectifs d'apprentissage
Appliquer les principes de la programmation orientée objet en PHP
Implémenter une architecture MVC (Modèle-Vue-Contrôleur)
Gérer les interactions utilisateur et le processus de commande
Utiliser une base de données pour stocker les menus, les commandes et les informations clients
Développer des fonctionnalités essentielles d'un système de commande en ligne
En nous basant sur le concept d'une pizzeria en ligne, nous avons exploré des fonctionnalités telles que la gestion du menu, le panier d'achat, le processus de commande et le suivi des livraisons.

🚀 Démarrage rapide
Pour lancer ce projet localement, suivez ces étapes :
Prérequis:
PHP 7.4 ou supérieur
MySQL
Composer pour la gestion des dépendances

Installation:

Lancez l'environnement Lando : lando start

Clonez ce dépôt sur votre machine locale :

git clone https://github.com/votre-username/papa-pizza.git

Naviguez dans le répertoire du projet :
cd papa-pizza

Installez les dépendances avec Composer :
 lando composer install

Configurez votre base de données dans le fichier config/database.php

Importez la structure de la base de données :

Importez la base de données : lando db-import bdd database_lamp.2024-06-10-1718030684.sql

L'application Papa Pizza intègre désormais Stripe comme solution de paiement, offrant une expérience de commande fluide et sécurisée pour les clients. Grâce à cette intégration, l'application peut accepter divers moyens de paiement en ligne, optimiser les taux de conversion et gérer efficacement les transactions.


🛠 Technologies utilisées
PHP 7.4+
MySQL
HTML5/CSS3
JavaScript
Bootstrap pour le design responsive
