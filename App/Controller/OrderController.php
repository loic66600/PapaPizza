<?php

namespace App\Controller;

use App\AppRepoManager;
use App\Model\Order;
use Core\Form\FormResult;
use Core\Controller\Controller;
use Core\Form\FormError;
use Core\Form\FormSuccess;
use Core\Session\Session;
use Laminas\Diactoros\ServerRequest;

class OrderController extends Controller
{
  /**
   * méthode qui permet de générer un numéro de commande unique
   */
  private function generateOrderNumber()
  {
    //je veux un numero de commande du type: FACT2406_00001 par exemple
    $order_number = 1;
    $order = AppRepoManager::getRm()->getOrderRepository()->findLastOrder();
    $order_number = str_pad($order + 1, 5, '0', STR_PAD_LEFT);
    $year = date('y');
    $month = date('m');

    $final = "FACT{$year}{$month}_{$order_number}";
    return $final;
  }

  public function addOrder(ServerRequest $request)
  {
    //on receptionne les données du formulaire
    $form_data = $request->getParsedBody();
    $form_result = new FormResult();
    //on redéfinit nos variables
    $order_number = $this->generateOrderNumber();
    $date_order = date('Y-m-d H:i:s');
    $status = Order::IN_CART;
    $user_id = $form_data['user_id'];
    $size_id = $form_data['size_id'];
    $has_order_in_cart = AppRepoManager::getRm()->getOrderRepository()->findLastStatusByUser($user_id, Order::IN_CART);
    $pizza_id = $form_data['pizza_id'];
    $quantity = $form_data['quantity'];
    $price = $form_data['price'] * $quantity;
    //on vérifie que la quantité est bien supérieur à 0
    if ($quantity <= 0) {
      $form_result->addError(new FormError('La quantité ne peut pas être 0'));
      //on vérifie que la quantité est bien inférieur à 10
    } elseif ($quantity > 10) {
      $form_result->addError(new FormError('La quantité ne peut pas être supérieur à 10'));
      //on vérifie que l'utilisateur n'a pas déjà une commande "mise au panier"
    } elseif (!$has_order_in_cart) {
      //on doit créer une nouvelle commande (order)
      //on reconstruit un tableau de données pour la commande
      $data_order = [
        'order_number' => $order_number,
        'date_order' => $date_order,
        'status' => $status,
        'user_id' => $user_id
      ];
      $order_id = AppRepoManager::getRm()->getOrderRepository()->insertOrder($data_order);

      if ($order_id) {
        //on peut inserer la ligne de commande
        //on reconstruit un tableau de données pour la ligne de commande
        $data_order_row = [
          'pizza_id' => $pizza_id,
          'quantity' => $quantity,
          'price' => $price,
          'order_id' => $order_id,
          'size_id' => $size_id
        ];
        $order_line = AppRepoManager::getRm()->getOrderRowRepository()->insertOrderRow($data_order_row);
        if ($order_line) {
          $form_result->addSuccess(new FormSuccess('Pizza ajouté au le panier'));
        } else {
          $form_result->addError(new FormError('Erreur lors de la création de la ligne de commande'));
        }
      } else {
        $form_result->addError(new FormError('Erreur lors de la création de la commande'));
      }
    } else {
      //si l'utilisateur a déjà une commande en cours
      //on récupère l'id de la commande en cours
      $order_id = AppRepoManager::getRm()->getOrderRepository()->findOrderIdByStatus($user_id);
      if ($order_id) {
        //on peut inserer la ligne de commande
        //on reconstruit un tableau de données pour la ligne de commande
        $data_order_row = [
          'pizza_id' => $pizza_id,
          'quantity' => $quantity,
          'price' => $price,
          'order_id' => $order_id,
          'size_id' => $size_id
        ];
        $order_line = AppRepoManager::getRm()->getOrderRowRepository()->insertOrderRow($data_order_row);
        if ($order_line) {
          $form_result->addSuccess(new FormSuccess('Pizza ajouté au le panier'));
        } else {
          $form_result->addError(new FormError('Erreur lors de la création de la ligne de commande'));
        }
      } else {
        $form_result->addError(new FormError('Erreur lors de la récupération de l\'id de la commande'));
      }
    }
    //si on a des erreur on les met en sessions
    if ($form_result->hasErrors()) {
      Session::set(Session::FORM_RESULT, $form_result);
      //on redirige sur la page detail de la pizza
      self::redirect('/pizza/' . $pizza_id);
    }

    //si on a des success on les met en sessions
    if ($form_result->getSuccessMessage()) {
      Session::remove(Session::FORM_RESULT);
      Session::set(Session::FORM_SUCCESS, $form_result);
      //on redirige sur la page detail de la pizza
      self::redirect('/pizza/' . $pizza_id);
    }
  }

  /**
   * méthode static qui regarde si on a des ligne dans le panier (en cours)
   * @return bool
   */
  public static function hasOrderInCart():bool
  {
    $user_id = Session::get(Session::USER)->id;
    $has_order_in_cart = AppRepoManager::getRm()->getOrderRepository()->findLastStatusByUser($user_id, Order::IN_CART);

    return $has_order_in_cart;
  }

  /**
   * méthode qui permet de modifier la quantité d'une ligne de commande
   * @param ServerRequest $request
   * @param int $id
   * @return void
   */
  public function updateOrder(ServerRequest $request, int $id)
  {
    $form_data = $request->getParsedBody();
    $form_result = new FormResult();
    $order_row_id = $form_data['order_row_id'];
    $quantity = $form_data['quantity'];
    $pizza_id = $form_data['pizza_id'];
    $size_id = $form_data['size_id'];
    $user_id = Session::get(Session::USER)->id;

    //on vérifie que la quantité est bien supérieur à 0
    if($quantity <= 0){
      $form_result->addError(new FormError('La quantité ne peut pas être 0'));
      //on vérifie que la quantité est bien inférieur à 10
    }elseif($quantity > 10){
      $form_result->addError(new FormError('La quantité ne peut pas être supérieur à 10'));
    }else{
      //on reconstruit un tableau de données pour mettre à jour la ligne de commande
      $data_order_line = [
        'id' => $order_row_id,
        'quantity' => $quantity,
        'pizza_id' => $pizza_id,
        'size_id' => $size_id
      ];
      //on appelle la méthode qui permet de modifier la ligne de commande
      $order_line = AppRepoManager::getRm()->getOrderRowRepository()->updateOrderRow($data_order_line);

      if($order_line){
        $form_result->addSuccess(new FormSuccess('Quantité modifié'));
      }else{
        $form_result->addError(new FormError('Erreur lors de la modification de la quantité'));
      }

      //si on a des erreur on les met en sessions
      if ($form_result->hasErrors()) {
        Session::set(Session::FORM_RESULT, $form_result);
        //on redirige sur la page panier
        self::redirect('/order/' . $user_id);
      }

      //si on a des success on les met en sessions
      if ($form_result->getSuccessMessage()) {
        Session::remove(Session::FORM_RESULT);
        Session::set(Session::FORM_SUCCESS, $form_result);
        //on redirige sur la page panier
        self::redirect('/order/' . $user_id);
      }

    }
  }

  /**
   * méthode qui permet de supprimer une ligne de commande
   * @param ServerRequest $request
   * @param int $id
   * @return void
   */
  public function deleteOrderRow(ServerRequest $request, int $id):void
  {
    $form_data = $request->getParsedBody();
    $form_result = new FormResult();
    $user_id = Session::get(Session::USER)->id;
    $order_row = AppRepoManager::getRm()->getOrderRowRepository()->deleteOrderRow($id);
    //si la suppression s'est bien passé, on regarde si la commande a encore des lignes
    if($order_row){
      $countOrder = AppRepoManager::getRm()->getOrderRowRepository()->countOrderRowByOrder($form_data['order_id']);
      $form_result->addSuccess(new FormSuccess('Pizza supprimé du panier'));
      if($countOrder <= 0){
        //si je n'ai plus de ligne de commande on supprime la commande
        AppRepoManager::getRm()->getOrderRepository()->deleteOrder($form_data['order_id']);
      }
    }else{
      $form_result->addError(new FormError('Erreur lors de la suppression de la pizza'));
    }

    //si on a des erreur on les met en sessions
    if ($form_result->hasErrors()) {
      Session::set(Session::FORM_RESULT, $form_result);
      //on redirige sur la page panier
      self::redirect('/order/' . $user_id);
    }

    //si on a des success on les met en sessions
    if ($form_result->getSuccessMessage()) {
      Session::remove(Session::FORM_RESULT);
      Session::set(Session::FORM_SUCCESS, $form_result);
      //on redirige sur la page panier
      self::redirect('/order/' . $user_id);
    }
  }
}
