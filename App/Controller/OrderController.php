<?php

namespace App\Controller;

use App\Model\Order;
use App\AppRepoManager;
use Core\Form\FormError;
use Core\Form\FormResult;
use Core\Session\Session;
use Core\Form\FormSuccess;
use Core\Controller\Controller;
use Laminas\Diactoros\ServerRequest;

class OrderController extends Controller
{

    /**
     * methode qui permet d'ajouter  un numero de commande unique
     * 
     */
    private function generateOrderNumber()
    {
        //je veux un numero de commande du typ :  FACT2406_00001 par exemple
        $order_number = 1;
        $order = AppRepoManager::getrm()->getOrderRepository()->findLastOrder();
        $order_number = str_pad($order + 1, 5, "0", STR_PAD_LEFT);
        $year = date('y');
        $month = date('m');
        $final = "FACT{$year}{$month}_{$order_number}";
        return $final;
    }
    public function addOrder(ServerRequest $_request)
    {
        //on receptionne  les donne du formulaire
        $form_data = $_request->getParsedBody();

        $form_result = new FormResult();
        //on redefinit nos variables
        $order_number = $this->generateOrderNumber();
        $date_order = date('Y-m-d H:i:s');
        $status = Order::IN_CART;
        $user_id = $form_data['user_id'];
        $has_order_in_cart = AppRepoManager::getrm()->getOrderRepository()->findLastStatusByUser($user_id, Order::IN_CART);
        // var_dump($has_order_in_cart);
        $pizza_id = $form_data['pizza_id'];
        $quantity = $form_data['quantity'];
        $price = $form_data['price'] * $quantity;
        //on verifie que la quantité est bien supperieur a 0
        if ($quantity <= 0) { //on verifie que la quantité est bien inferieur a 10
            $form_result->adderror(new FormError('la quantité doit etre inferieur a 0'));
        } elseif ($quantity > 10) { //on verifie que la quantité est bien inferieur a 10
            $form_result->adderror(new FormError('la quantité doit etre superieur a 10'));
        } elseif (!$has_order_in_cart) {
            //on doit cree une nouvelle commande (order)
            $data_order = [
                'order_number' => $order_number,
                'date_order' => $date_order,
                'status' => $status,
                'user_id' => $user_id
            ];
            $order_id = AppRepoManager::getrm()->getOrderRepository()->insertOrder($data_order);

            if ($order_id) {
                //on peu inseré la ligne de commande dan s le if
                //on reconstrite le tableau de donné pour la ligne de commande
                $data_order_row = [
                    'pizza_id' => $pizza_id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'order_id' => $order_id
                ];
                $order_line = AppRepoManager::getrm()->getOrderRowRepository()->insertOrderRow($data_order_row);
                if ($order_line) {
                    $form_result->addSuccess(new FormSuccess('la pizza bien été ajouter'));
                } else {
                    $form_result->adderror(new FormError('Erreur lors de l\'ajout de la commande'));
                }
            } else {
                $form_result->adderror(new FormError('la commande n\'a pas pu etre ajouter'));
            }
        } else {
            //si l utilisateur a une commande en cours
            //on recuper l id de la commande en cours
            $order_id = AppRepoManager::getrm()->getOrderRepository()->findOrderIdByStatus($user_id);
            if ($order_id) {
                //on peu inseré la ligne de commande dan s le if
                //on reconstrite le tableau de donné pour la ligne de commande
                $data_order_row = [
                    'pizza_id' => $pizza_id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'order_id' => $order_id
                ];
                $order_line = AppRepoManager::getrm()->getOrderRowRepository()->insertOrderRow($data_order_row);
                if ($order_line) {
                    $form_result->addSuccess(new FormSuccess('la pizza bien ete ajouter'));
                } else {
                    $form_result->adderror(new FormError('Erreur lors de l\'id de la commande'));
                }
            }
        }
        //si on des erreur on les affiche eb session
        if($form_result->hasErrors()){
            Session::set(Session::FORM_RESULT, $form_result);
            //on redirige sur la page detaile de la pizza
            self::redirect('/pizza/'.$pizza_id);
            
        }
        //si on des succes on les affiche en session
        if($form_result->getSuccessMessage()){
            Session::remove(Session::FORM_RESULT);
            Session::set(Session::FORM_SUCCESS, $form_result);
            //on redirige sur la page detaile de la pizza   
            self::redirect('/pizza/'. $pizza_id);
        }
    }
}
