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
   * méthode qui permet de récupérer toutes les pizzas de l'admin
   * @return array
   */
  public function getAllPizzas(): array
  {
    //on déclare un tableau vide
    $array_result = [];

    //on crée la requête SQL
    $q = sprintf(
      'SELECT p.`id`, p.`name`, p.`image_path` 
      FROM %1$s AS p
      INNER JOIN %2$s AS u ON p.`user_id` = u.`id` 
      WHERE u.`is_admin` = 1 
      AND p.`is_active` = 1
      ',
      $this->getTableName(), //correspond au %1$s
      AppRepoManager::getRm()->getUserRepository()->getTableName() //correspond au %2$s
    );

    //on peut directement executer la requete
    $stmt = $this->pdo->query($q);
    //on vérifie que la requete est bien executée
    if (!$stmt) return $array_result;
    //on récupère les données que l'on met dans notre tableau
    while ($row_data = $stmt->fetch()) {
      //a chaque passage de la boucle on instancie un objet pizza
      $array_result[] = new Pizza($row_data);
    }
    //on retourne le tableau fraichement rempli
    return $array_result;
  }

  /**
   * méthode qui permet de récupérer une pizza grace à son id
   * @param int $pizza_id
   * @return ?Pizza
   */
  public function getPizzaById(int $pizza_id): ?Pizza
  {
    //on crée la requete SQL
    $q = sprintf(
      'SELECT * FROM %s WHERE `id` = :id',
      $this->getTableName()
    );

    //on prépare la requete
    $stmt = $this->pdo->prepare($q);

    //on vérifie que la requete est bien préparée
    if (!$stmt) return null;

    //on execute la requete en passant les paramètres
    $stmt->execute(['id' => $pizza_id]);

    //on récupère le résultat
    $result = $stmt->fetch();

    //si je n'ai pas de résultat, je retourne null
    if (!$result) return null;

    //si j'ai un résultat, j'instancie un objet Pizza
    $pizza = new Pizza($result);

    //on va hydrater les ingredients de la pizza
    $pizza->ingredients = AppRepoManager::getRm()->getPizzaIngredientRepository()->getIngredientByPizzaId($pizza_id);
    //on va hydrater les prix de la pizza
    $pizza->prices = AppRepoManager::getRm()->getPriceRepository()->getPriceByPizzaId($pizza_id);
    //je retourne l'objet Pizza
    return $pizza;
  }

  /**
   * methode qui permet d ajouter une nouvelle pizza
   * @param array $data
   * @return int
   * 
   */
  public function insertpizza(array $data): ?int
  {
    //on crée la requête SQL
    $q = sprintf(
      'INSERT INTO %s
      (`name`, `image_path`, `user_id`, `is_active`)
      VALUES
      (:name, :image_path, :user_id, :is_active)',
      $this->getTableName()
    );

    //on prépare la requête
    $stmt = $this->pdo->prepare($q);

    //on verifie que la requête est bien préparée
    if (!$stmt) return null;

    //on execute la requête en passant les paramètres
    $stmt->execute($data);

    //on retourne le dernier id inseré
    return $this->pdo->lastInsertId();
  }

  /**on doit récupérer les pizzas custom de l utilisateur getPizzaByUser
   * @param int $user_id
   * @return array
   * 
   */
  public function getPizzaByUser(int $id): array
  {
    $array_result = [];

    //on crée la requête SQL
    $q = sprintf(
      ' SELECT *
      FROM %s
      WHERE `user_id` = :id AND `is_active` = 1
      ',
      $this->getTableName(),
    );
    //on prépare la requete
    $stmt = $this->pdo->prepare($q);

    //on vérifie que la requete est bien préparée
    if (!$stmt) return $array_result;

    //on execute la requete en passant les paramètres
    $stmt->execute(['id' => $id]);

    //on récupère les données
    while ($row_data = $stmt->fetch()) {
      $pizza = new Pizza($row_data);
      //on va hydrater les ingerdients de la pizza
      $pizza->ingredients = AppRepoManager::getRm()->getPizzaIngredientRepository()->getIngredientByPizzaId($pizza->id);

      //on va hydrater les prix de la pizza
      $pizza->prices = AppRepoManager::getRm()->getPriceRepository()->getPriceByPizzaId($pizza->id);
      $array_result[] = $pizza;
    }

    return $array_result;
  }

  /**
   * methode qui permet de desactiver une pizza
   * @param int $id
   * @return bool
   */
  public function deletepizza(int $id): bool
  {
    //on crée la requête SQL
    $q = sprintf(
      'UPDATE `%s`
      SET `is_active` = 0
      WHERE `id` = :id',
      $this->getTableName()
    );

    //on prépare la requête
    $stmt = $this->pdo->prepare($q);

    //on verifie que la requête est bien préparée
    if (!$stmt) return false;

    //on execute la requête en passant les paramètres
    return $stmt->execute(['id' => $id]);
  }
}
