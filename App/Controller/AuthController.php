<?php

namespace App\Controller;

use App\AppRepoManager;
use App\Model\User;
use Core\View\View;
use Core\Form\FormError;
use Core\Form\FormResult;
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

        //on reception les donnees du formulaire
        $data_form = $request->getParsedBody();

        //on instancie formResult pour stoker les message d erreur
        $form_result = new FormResult();
        //on doit cree une instance de user
        $user = new User();

        //on s occupe de toutes les verifications
        if (
            empty($data_form['email']) ||
            empty($data_form['password']) ||
            empty($data_form['password_confirm']) ||
            empty($data_form['lastname']) ||
            empty($data_form['firstname']) ||
            empty($data_form['phone'])

        ) {
            $form_result->addError(new FormError('Veuillez remplir tous les champs'));
        } elseif ($data_form['password'] !== $data_form['password_confirm']) {
            $form_result->addError(new FormError('Les mots de passe ne sont pas identiques'));
        } elseif (!$this->validEmail($data_form['email'])) {
            $form_result->addError(new FormError('Le l\'email est invalide'));
        } elseif (!$this->validPassword($data_form['password'])) {
            $form_result->addError(new FormError('Le format du mot de passe doit contenir 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère speciaux et 8 caractères au minimum'));
        } elseif ($this->userExists($data_form['email'])) {
            $form_result->addError(new FormError('Cet email existe déja'));
        } else {
            $data_user = [
                'email' => strtolower($this->validInput($data_form['email'])),
                'password' => password_hash($this->validInput($data_form['password']), PASSWORD_BCRYPT),
                'lastname' => $this->validInput($data_form['lastname']),
                'firstname' => $this->validInput($data_form['firstname']),
                'phone' => $this->validInput($data_form['phone'])
            ];
            AppRepoManager::getrm()->getUserRepository()->addUser($data_user);
        }
        if ($form_result->hasErrors()) {
            Session::set(Session::FORM_RESULT, $form_result);
            self::redirect('/inscription');
        }
        $user->password = '';
        Session::set(Session::USER, $user);
        Session::remove(Session::FORM_RESULT);
        self::redirect('/');
    }

    /**
     * methode qui permet de traiter le formulaire de connexion
     */
    public function login(ServerRequest $request)
    {

        //on reception les donnees du formulaire
        $data_form = $request->getParsedBody();
        //on instancie formResult pour stoker les message d erreur
        $form_result = new FormResult();
        //on doit cree une instance de user
        $user = new User();

        //on s occupe de toutes les verifications
        if (
            empty($data_form['email']) || empty($data_form['password'])
        ) {
        } elseif (!$this->validEmail($data_form['email'])) {
            $form_result->addError(new FormError('Le l\'email est invalide'));
        } else {
            $email = strtolower($this->validInput($data_form['email']));
            //on verifie qu a a bien un utilisateur avec cette email
            $user = AppRepoManager::getrm()->getUserRepository()->findUserByEmail($email);
            if (is_null($user) || !password_verify($this->validInput($data_form['password']), $user->password)) {
                $form_result->addError(new FormError('Email ou mot de passe incorrect'));
            }
        }
        if ($form_result->hasErrors()) {
            Session::set(Session::FORM_RESULT, $form_result);
            self::redirect('/connexion');
        }
        $user->password = '';
        Session::set(Session::USER, $user);
        Session::remove(Session::FORM_RESULT);
        self::redirect('/');
    }




    /**
     * methode qui verifie si l email est bon
     * @param string $email
     *@return void
     */
    public function validEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    /**
     * methode qui verifie le mot de passe contient  1 majuscule, 1 minuscule, 1 chiffre, 1 caractère speciaux et 8 caractères au minimum
     * @param string $password
     * @return bool
     */
    public function validPassword(string $password): bool
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
    }

    /**
     * methode qui vérifie que si l'utilisateur existe
     * @param string $email
     * @param return bool
     */
    public function userExists(string $email): bool
    {
        $user = AppRepoManager::getrm()->getUserRepository()->findUserByEmail($email);
        return !is_null($user);
    }

    /*
    *methode qui securise les donnes
    *@param string $data
    * @return string
    */
    public function validInput(string $data): string
    {
        $data = trim($data);
        $data = strip_tags($data);
        $data = stripcslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    //methode qui verifie si un utilisateur est en session
    public static function isAuth(): bool
    {
        return !is_null(Session::get(Session::USER));
    }
}
