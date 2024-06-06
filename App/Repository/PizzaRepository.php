<?php

namespace App\Repository;

use App\Model\Pizza;
use App\AppRepoManager;
use Core\Repository\Repository;

class PizzaRepository extends Repository
{
  public function getTableName(): string
  {
    return 'pizza';
  }
  /**
   * methode qui permet de récuperé toutes les pizzas de l admin
   * @return array
   */
  public function getAllPizzas(): array
  {
    // on déclare un tableau vide
    $array_result = [];
    //on crée notre requete SQL
    $q = sprintf(
      'SELECT p.`id`, p.`name`, p.`image_path`
      FROM %1$s AS p
      INNER JOIN %2$s AS u ON p.`user_id` = u.`id`
      WHERE u.`is_admin` = 1
      AND p.`is_active` = 1
      ',
      $this->getTableName(), //corespond au %1$s
      AppRepoManager::getrm()->getUserRepository()->getTableName() //corespond au %2$s
    );

    //on execute la requete directemente
    $stmt = $this->pdo->query($q);
    //on vérifie que la requete est bien preparer
    if (!$stmt) return $array_result;

    //on peut recuperer les données de la requete
    while ($row_data = $stmt->fetch()) {
      //a chaque passage de la boucle on instancie un objet Pizza
      $array_result[] = new Pizza($row_data);
    }
    return $array_result;
  }

  /**
   * methode qui permet de récuperé une pizza par son id
   * @param int $pizza_id
   * @return Pizza
   */
  public function getPizzaById(int $pizza_id): ?Pizza
  {
    //on crée notre requete SQL
    $q = sprintf(
      'SELECT* FROM %s WHERE `id` = :id',
      $this->getTableName()
    );
    //on prepare la requete
    $stmt = $this->pdo->prepare($q);
    //on verifie que la requete est bien preparer
    if (!$stmt) return null;
    //si tous est bon on execute la requete
    $stmt->execute(['id' => $pizza_id]);
    //on peut recuperer les données de la requete
    $result = $stmt->fetch();
    //si je n ai pas de résultat on retourne null
    if (empty($result)) return null;
    //si j ai un resultat on retourne un objet Pizza
    $pizza = new Pizza($result);
//on va hydrager l objet pizza avec les données de la requete
    $pizza->ingredients = AppRepoManager::getrm()->getPizzaIngredientRepository()->getIngredientByPizzaId($pizza_id);
    //on va hydrager l objet pizza avec les données de la requete
    $pizza->prices = AppRepoManager::getrm()->getPriceRepository()->getPriceByPizzaId($pizza_id);


    return $pizza;

    
  }
}
