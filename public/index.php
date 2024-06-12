<?php


use App\App;
use Dotenv\Dotenv;

const DS = DIRECTORY_SEPARATOR;

define('PATH_ROOT', dirname(__DIR__) . DS);

require_once PATH_ROOT . 'vendor/autoload.php';

//permet de charger le fichier .env
Dotenv::createImmutable(PATH_ROOT)->load();

//pour recupérer les infos du .env on utilise $_ENV['key']
define('STRIPE_SK', $_ENV['STRIPE_SK']);
define('STRIPE_PK', $_ENV['STRIPE_PK']);


App::getApp()->start();
