<?php

namespace App\Controller;

use App\Model\Order;
use App\AppRepoManager;
use Core\Form\FormError;
use Stripe\StripeClient;
use Core\Form\FormResult;
use Core\Session\Session;
use Core\Form\FormSuccess;
use Core\Controller\Controller;
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
  public static function hasOrderInCart(): bool
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
    if ($quantity <= 0) {
      $form_result->addError(new FormError('La quantité ne peut pas être 0'));
      //on vérifie que la quantité est bien inférieur à 10
    } elseif ($quantity > 10) {
      $form_result->addError(new FormError('La quantité ne peut pas être supérieur à 10'));
    } else {
      //on reconstruit un tableau de données pour mettre à jour la ligne de commande
      $data_order_line = [
        'id' => $order_row_id,
        'quantity' => $quantity,
        'pizza_id' => $pizza_id,
        'size_id' => $size_id
      ];
      //on appelle la méthode qui permet de modifier la ligne de commande
      $order_line = AppRepoManager::getRm()->getOrderRowRepository()->updateOrderRow($data_order_line);

      if ($order_line) {
        $form_result->addSuccess(new FormSuccess('Quantité modifié'));
      } else {
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
  public function deleteOrderRow(ServerRequest $request, int $id): void
  {
    $form_data = $request->getParsedBody();
    $form_result = new FormResult();
    $user_id = Session::get(Session::USER)->id;
    $order_row = AppRepoManager::getRm()->getOrderRowRepository()->deleteOrderRow($id);
    //si la suppression s'est bien passé, on regarde si la commande a encore des lignes
    if ($order_row) {
      $countOrder = AppRepoManager::getRm()->getOrderRowRepository()->countOrderRowByOrder($form_data['order_id']);
      $form_result->addSuccess(new FormSuccess('Pizza supprimé du panier'));
      if ($countOrder <= 0) {
        //si je n'ai plus de ligne de commande on supprime la commande
        AppRepoManager::getRm()->getOrderRepository()->deleteOrder($form_data['order_id']);
      }
    } else {
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

  /** 
   * METHODE POUR EFFECTUER UN PAYMENT SUR STRIPE
   * @param int $order_id
   * @return void
   * 
   */
  public function paymentStripe(int $order_id): void
  {
    //on instancie stripe pour lui passe les clefs
    //permet de cabler l api de stripe avec notre compte stripe
    $stripe = new StripeClient(STRIPE_SK);

    if (!AuthController::isAuth()) $this->redirect('/');
    $user_id = Session::get(Session::USER)->id;

    //on recupérer les lignes de commande du panier
    $data = AppRepoManager::getRm()->getOrderRepository()->findOrderByIdWithRow($order_id);

    if (!$data) $this->redirect('/');

    //on va redefinir ds variables

    $oreder_number = $data->order_number;
    $name = "commande n° {$oreder_number}";
    //on declare un tableau vide pour stocker les payement intents Stripe
    $product_stripe = [];

    //on boucle sur les lignes de commande
    foreach ($data->order_rows as $row) {

      $product_stripe[] = [
        'price_data' => [
          'currency' => 'eur',
          'product_data' => [
            'name' => $row->pizza->name,
            'description' => "{$name} :\n {$row->pizza->name} x {$row->quantity}",
          ],
          'unit_amount' => $row->price * 100,
        ],
        'quantity' =>1,
      ];
    }
    //on crée une checkout session de stripe avec le tableau de produits
    $checkout_session = $stripe->checkout->sessions->create([
      'line_items' => $product_stripe,
      'mode' => 'payment',
      'success_url' => 'http://14-ern24-papapizza.lndo.site/order/success-order/' . $order_id,
      'cancel_url' => 'http://14-ern24-papapizza.lndo.site/order/' . $user_id,

    ]);
    header("HTTP/1.1 303 See Other");
    header('Location: ' . $checkout_session->url);
   
  }

  /**
   * METHODE POUR EFFECTUER UN PYMENT SUR STRIPE
   * @param int $order_id
   * @return void
   */
  public function successOrder(int $order_id): void
  {
    $form_result = new FormResult();
    //on va recupérer la commande
    $order = AppRepoManager::getRm()->getOrderRepository()->findOrderByIdWithRow($order_id);
    //on va reconstruire le tableau de lignes de commande
    $data = [
      'id' => $order->id,
      'status' => Order::VALIDATED
    ];

    $order = AppRepoManager::getRm()->getOrderRepository()->updateOrder($data);
    $user_id = Session::get(Session::USER)->id;

    if(!$order) {
      $form_result->addError(new FormError('Erreur lors de la validation de la commande'));
    }else {
      $form_result->addSuccess(new FormSuccess('Commande valide'));
    }
        //si on a des erreur on les met en sessions
        if ($form_result->hasErrors()) {
          Session::set(Session::FORM_RESULT, $form_result);
          //on redirige sur la page detail de la pizza
          self::redirect('/order/' . $user_id);
        }
    
        //si on a des success on les met en sessions
        if ($form_result->getSuccessMessage()) {
          Session::remove(Session::FORM_RESULT);
          Session::set(Session::FORM_SUCCESS, $form_result);
          //on redirige sur la page panier
          self::redirect('/user/list-order/' . $user_id);
        }
    
  }

 
    /**
   * METHODE POUR annuler une commande
   * @param int $id
   * @return void
   */
  public function cancelOrder(int $id): void
  {
    $form_result = new FormResult();
    //on va recupérer la commande
    $order = AppRepoManager::getRm()->getOrderRepository()->findOrderByIdWithRow($id);
    //on va reconstruire le tableau de lignes de commande
    $data = [
      'id' => $id,
      'status' => Order::CANCELED
    ];

    $order = AppRepoManager::getRm()->getOrderRepository()->updateOrder($data);
    $user_id = Session::get(Session::USER)->id;

    if(!$order) {
      $form_result->addError(new FormError('Erreur lors de l annulation de la commande'));
    }else {
      $form_result->addSuccess(new FormSuccess('Commande annulée'));
    }
        //si on a des erreur on les met en sessions
        if ($form_result->hasErrors()) {
          Session::set(Session::FORM_RESULT, $form_result);
          //on redirige sur la page detail de la pizza
          self::redirect('/user/list-order/' . $user_id);
        }
    
        //si on a des success on les met en sessions
        if ($form_result->getSuccessMessage()) {
          Session::remove(Session::FORM_RESULT);
          Session::set(Session::FORM_SUCCESS, $form_result);
          //on redirige sur la page panier
          self::redirect('/user/list-order/' . $user_id);
        }
    
  }

      /**
   * METHODE POUR annuler une commande
   * @param int $id
   * @return void
   */
  public function reactivatedOrder(int $id): void
  {
    $form_result = new FormResult();
    //on va recupérer la commande
    $order = AppRepoManager::getRm()->getOrderRepository()->findOrderByIdWithRow($id);
    //on va reconstruire le tableau de lignes de commande
    $data = [
      'id' => $id,
      'status' => Order::VALIDATED
    ];

    $order = AppRepoManager::getRm()->getOrderRepository()->updateOrder($data);
    $user_id = Session::get(Session::USER)->id;

    if(!$order) {
      $form_result->addError(new FormError('Erreur lors de la réactivation de la commande'));
    }else {
      $form_result->addSuccess(new FormSuccess('Commande réactivée'));
    }
        //si on a des erreur on les met en sessions
        if ($form_result->hasErrors()) {
          Session::set(Session::FORM_RESULT, $form_result);
          //on redirige sur la page detail de la pizza
          self::redirect('/user/list-order/' . $user_id);
        }
    
        //si on a des success on les met en sessions
        if ($form_result->getSuccessMessage()) {
          Session::remove(Session::FORM_RESULT);
          Session::set(Session::FORM_SUCCESS, $form_result);
          //on redirige sur la page panier
          self::redirect('/user/list-order/' . $user_id);
        }
    
  }


}
