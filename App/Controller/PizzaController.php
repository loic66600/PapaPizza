<?php

namespace App\Controller;

use Core\View\View;
use App\AppRepoManager;
use Core\Session\Session;
use Core\Controller\Controller;

class PizzaController extends Controller
{

   /**
    * methode qui revoit la vue d'accueil
    * @return void
    */
   public function home()
   {
      $view = new View('home/home');
      $view->render();
   }
   /**
    * methode qui revoit la vue des pizzas
    * @return void

    */
   public function getPizzas(): void
   {
      //le controler dooit récupere le tableau de pizzas pour le donnée a la vue
      $pizzas = AppRepoManager::getrm()->getPizzaRepository()->getAllPizzas();
      $view_data = [
         'h1' => 'Notre carte',
         'pizzas' => $pizzas
      ];

      $view = new View('home/pizza');
      $view->render($view_data);
   }
   /**
    * methode qui revoit la vue d'une pizza
    * @return void
    * @param int $id
    *
    */
   public function getPizzaById(int $id): void

   {
     
      $view_data = [ 
         'pizza' => AppRepoManager::getrm()->getPizzaRepository()->getPizzaById($id),
         'form_result'=> Session::get(Session::FORM_RESULT),
         'form_success'=> Session::get(Session::FORM_SUCCESS),
      ];

      $view = new View('home/pizza_detail');
      $view->render($view_data);
     
   }
}
