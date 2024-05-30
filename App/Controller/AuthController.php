<?php

namespace App\Controller;

use Core\View\View;
use Core\Session\Session;
use Core\Controller\Controller;
use Laminas\Diactoros\ServerRequest;

class AuthController extends Controller
{
    /**
     * methode qui renvoie la vue de formulaire de connexion
     * @return void
     * 
     */
    public function loginForm()
    {
        $VIEW = new View('auth/login');
        $view_data = [
            'form_result' => Session::get(Session::FORM_RESULT),
        ];

        $VIEW->render($view_data);
    }
    /**
     * methode qui renvoie la vue de formulaire de connexion
     * @return void
     * 
     */
    public function registerForm()
    {
        $VIEW = new View('auth/register');
        $view_data = [
            'form_result' => Session::get(Session::FORM_RESULT),
        ];

        $VIEW->render($view_data);
    }


    /**methode qui permet de traiter le formulaire d enregistrment
     * 
     */
    public function register(ServerRequest $request)
    {
        $data_form = $request->getParsedBody();
        var_dump($data_form);
    }

    /**
     * methode qui permet de traiter le formulaire de connexion
     */
    public function login()
    {
    }
}
