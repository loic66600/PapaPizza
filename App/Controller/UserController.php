<?php

namespace App\Controller;

use Core\View\View;
use App\AppRepoManager;
use Core\Form\FormError;
use Core\Form\FormResult;
use Core\Session\Session;
use Core\Form\FormSuccess;
use Core\Controller\Controller;

class UserController extends Controller
{
  /**
   * méthode qui renvoi la vue du panier d'un utilisateur
   * @param int|string $id
   * @return void
   */
  public function order(int|string $id): void
  {
    //on récupère la commande en cours avec toutes ses lignes de commande
    $order = AppRepoManager::getRm()->getOrderRepository()->findOrderInProgressWithOrderRow($id);
    //on récupère le total de la commande
    $total = $order ? AppRepoManager::getRm()->getOrderRowRepository()->findTotalPriceByOrder($order->id) : 0;
    //on récupère les quantité de pizzas pour chaque ligne de commande
    $countRow = $order ? AppRepoManager::getRm()->getOrderRowRepository()->countOrderRow($order->id) : 0;

    $view_data = [
      'order' => $order,
      'total' => $total,
      'count_row' => $countRow,
      'form_result' => Session::get(Session::FORM_RESULT),
      'form_success' => Session::get(Session::FORM_SUCCESS)
    ];

    $view = new View('user/order');

    $view->render($view_data);
  }

  /**
   * methode pour afficher le formulaire pizza custom
   * @param int $id
   * @return void
   */
  public function createPizza(int $id): void
  {
    $view_data = [
      'form_result' => Session::get(Session::FORM_RESULT),
      'form_success' => Session::get(Session::FORM_SUCCESS)
    ];

    $view = new View('user/createPizza');
    $view->render($view_data);
  }

  /**
   * methode qui retourne la liste des pizza d un utilisateur
   * @param int $id
   * @return void
   */

  public function listCustomPizza(int $id): void
  {
    //on recupere la liste des pizzacustoms
    $view_data = [
      'h1' => 'Liste des pizzas custom',
      'pizzas' => AppRepoManager::getRm()->getPizzaRepository()->getPizzaByUser($id),
      'form_result' => Session::get(Session::FORM_RESULT),
      'form_success' => Session::get(Session::FORM_SUCCESS)
    ];
    $View = new View('user/pizzas');
    $View->render($view_data);
  }
  /**
   * methode qui permet de supprimer une pizza custom
   * @param int $id
   * @return void
   */

  public function deletepizza(int $id): void
  {
    $form_result = new FormResult();
    $user_id = Session::get(Session::USER)->id;

    //appel de la methode qui desactive de la pizza
    $deletepizza = AppRepoManager::getRm()->getPizzaRepository()->deletePizza($id);

    //on vérifie le retour de la methode
    if ($deletepizza) {
      $form_result->addError(new FormError('erreur lors de la suppression de la pizza'));
    } else {
      $form_result->addSuccess(new FormSuccess('pizza supprimé'));
    }
    //si on a des erreur on les met en sessions
    if ($form_result->hasErrors()) {
      Session::set(Session::FORM_RESULT, $form_result);
      //on redirige sur la page panier
      self::redirect('/user/list-custom-pizza/' . $user_id);
    }

    //si on a des success on les met en sessions
    if ($form_result->getSuccessMessage()) {
      Session::remove(Session::FORM_RESULT);
      Session::set(Session::FORM_SUCCESS, $form_result);
      //on redirige sur la page pizza custom
      self::redirect('/user/list-custom-pizza/' . $user_id);
    }
  }

  /**
   * methode quiretourne la liste des commande d'un utilisateur
   * @param int $id
   * @return void
   * 
   */

  public function listOrder(int $id): void
  {
    $view_data = [
      'orders' => AppRepoManager::getRm()->getOrderRepository()->findOrderByUser($id),
      'form_result' => Session::get(Session::FORM_RESULT),
      'form_success' => Session::get(Session::FORM_SUCCESS)
    ];
    $view = new View('user/listOrder');

    $view->render($view_data);
  }
}
