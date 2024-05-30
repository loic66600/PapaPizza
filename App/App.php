<?php

namespace App;

use App\Controller\AuthController;
use App\Controller\PizzaController;
use MiladRahimi\PhpRouter\Router;
use Core\Database\DatabaseConfigInterface;
use MiladRahimi\PhpRouter\Exceptions\RouteNotFoundException;
use MiladRahimi\PhpRouter\Exceptions\InvalidCallableException;

class App implements DatabaseConfigInterface
{
    //on définit des constantes
    private const DB_HOST = 'database';
    private const DB_NAME = 'database_lamp';
    private const DB_USER = 'admin';
    private const DB_PASS = 'admin';

    private static ?self $instance = null;
    //on crée une methode public qui sera appele au demarage de l appli
    public static function getApp(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    //on vrée une propriété privée pour stocker le routeur
    private Router $router;
    //méthode qui récupere le sinfis router
    public function getRouter()
    {
        return $this->router;
    }
    private function __construct()
    {
        $this->router = Router::create();
    }
    //on a 3 méthodes a définir
    //1 metgode start pour activé le routeur
    public function start(): void
    {
        //enregistrement des routes
        $this->registerRoutes();
        //demarage du routeur
        $this->startRouter();
    }
    //2 méthode qui enregistre les routes
    private function registerRoutes(): void
    {
        // partie auth
        //connexion
        $this->router->get('/connexion', [AuthController::class, 'loginForm']);
        $this->router->post('login', [AuthController::class, 'login']);
        $this->router->get('/inscription', [AuthController::class, 'registerForm']);
        $this->router->post('/register', [AuthController::class, 'register']);


        //parti pizza
        $this->router->get('/', [PizzaController::class, 'home']);
    }
    //3 methode qui demare le routeur
    private function startRouter(): void
    {
        try {
            $this->router->dispatch();
        } catch (RouteNotFoundException $e) {
            echo $e;
        } catch (InvalidCallableException $e) {
            echo $e;
        }
    }









    public function getHost(): string
    {
        return self::DB_HOST;
    }
    public function getName(): string
    {
        return self::DB_NAME;
    }
    public function getUser(): string
    {
        return self::DB_USER;
    }
    public function getPass(): string
    {
        return self::DB_PASS;
    }
}
