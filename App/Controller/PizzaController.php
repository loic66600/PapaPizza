<?php

namespace App\Controller;

use Core\View\View;
use Core\Controller\Controller;

class PizzaController extends Controller
{
   public function home()
   {


      $view = new View('home/home');
      $view->render();
   }
}
