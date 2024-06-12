<?php

namespace App\Controller;

use Core\View\View;
use App\AppRepoManager;
use Core\Form\FormError;
use Core\Form\FormResult;
use Core\Session\Session;
use Core\Form\FormSuccess;
use Core\Controller\Controller;
use Laminas\Diactoros\ServerRequest;

class PizzaController extends Controller
{
  /**
   * méthode qui renvoie la vue de la page d'accueil
   * @return void
   */
  public function home()
  {
    //preparation des données à transmettre à la vue


    $view = new View('home/home');
    $view->render();
  }

  /**
   * méthode qui renvoie la vue de la liste des pizzas
   * @return void
   */
  public function getPizzas(): void
  {
    //le controlleur doit récupérer le tableau de pizzas pour le donnée à la vue
    $view_data = [
      'h1' => 'Notre carte',
      'pizzas' => AppRepoManager::getRm()->getPizzaRepository()->getAllPizzas()
    ];

    $view = new View('home/pizzas');
    $view->render($view_data);
  }

  /**
   * méthode qui renvoie la vue d'une pizza grace à son id
   * @param int $id
   * @return void
   */
  public function getPizzaById(int $id): void
  {

    $view_data = [
      'pizza' => AppRepoManager::getRm()->getPizzaRepository()->getPizzaById($id),
      'form_result' => Session::get(Session::FORM_RESULT),
      'form_success' => Session::get(Session::FORM_SUCCESS),
    ];


    $view = new View('home/pizza_detail');
    $view->render($view_data);
  }

  /**
   * methode qui perment d ajouter une pizza custom
   * @param ServerRequest $request
   * @return void
   */
  public function addCustomPizzaForm(ServerRequest $request): void
  {
    $data_form = $request->getParsedBody();
    $form_result = new FormResult();
    //on redefinit les variables
    $ingredients = $data_form['ingredients'] ?? [];
    $name = $data_form['name'] ?? '';
    $user_id = $data_form['user_id'] ?? '';
    $size_id = $data_form['size_id'] ?? '';
    $array_ingredients = count($ingredients);
    $image_path = 'pizza-custom.png';

    //on pase au verification
    if (empty($name) || empty($ingredients) || empty($user_id) || empty($size_id)) {
      $form_result->addError(new FormError('Veuillez remplir tous les champs'));
    } elseif ($array_ingredients < 2) {
      $form_result->addError(new FormError('Veuillez ajouter au moins deux ingredients'));
    } elseif ($array_ingredients > 10) {
      $form_result->addError(new FormError('Veuillez ajouter moins de 10 ingredients'));
    } else {
      //on definit un prix fixe par taille plus ingredients
      if ($size_id == 1) {
        $price = 6 + ($array_ingredients * 1);
      } elseif ($size_id == 2) {
        $price = 7 + ($array_ingredients * 1.25);
      } else {
        $price = 8 + ($array_ingredients * 1.5);
      }

      //on peut reconstruire un tableau de donnees pour les ingredients
      $pizza_data = [
        'name' => htmlspecialchars(trim($name)),
        'image_path' => $image_path,
        'user_id' => intval($user_id),
        'is_active' => 1,
      ];
      $pizza_id = AppRepoManager::getRm()->getPizzaRepository()->insertpizza($pizza_data);
      if (is_null($pizza_id)) {
        $form_result->addError(new FormError('Une erreur est survenue lors de la création de la pizza'));
      }

      //on recupere l id de la pizza qui vien detre creé
      foreach ($ingredients as $ingredient) {
        //on reconstrui un tableau de donnees pour les ingredients
        $pizza_ingredient_data = [
          'pizza_id' => intval($pizza_id),
          'ingredient_id' => intval($ingredient),
          'unit_id' => 5,
          'quantity' => 1
        ];
        //toujour dans la boucle on insert la pizza et les ingredients dans pizza_ingredient
        $pizza_ingredient = AppRepoManager::getRm()->getPizzaIngredientRepository()->insertpizzaIngredient($pizza_ingredient_data);
        if (!$pizza_ingredient) {
          $form_result->addError(new FormError('Une erreur est survenue lors de l ajout de l ingredient'));
        }
      }

      //on va reconstruire un tableau pour insere les prix
      $price_data = [
        'pizza_id' => intval($pizza_id),
        'size_id' => intval($size_id),
        'price' => floatval($price)
      ];
      $pizza_price = AppRepoManager::getRm()->getPriceRepository()->insertPrice($price_data);

      if (!$pizza_price) {
        $form_result->addError(new FormError('Une erreur est survenue lors de l ajout du prix'));
      }
      //si tout est ok on peut ajouter un message de success
      $form_result->addSuccess(new FormSuccess('La pizza a bien été ajoutée'));
    }
    //si on a des erreur on les met en sessions
    if ($form_result->hasErrors()) {
      Session::set(Session::FORM_RESULT, $form_result);
      //on redirige sur la page detail de la pizza
      self::redirect('/user/create-pizza/' . $user_id);
    }

    //si on a des success on les met en sessions
    if ($form_result->getSuccessMessage()) {
      Session::remove(Session::FORM_RESULT);
      Session::set(Session::FORM_SUCCESS, $form_result);
      //on redirige sur la page detail de la pizza
      self::redirect('/user/list-custom-pizza/' . $user_id); //TODO: CHANGER LA REDIRECTION VERS LISTE DES PIZZA CUSTOM
    }
  }
}
