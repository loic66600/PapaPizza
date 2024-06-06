<?php

namespace App\Repository;

use App\AppRepoManager;
use App\Model\Ingredient;
use Core\Repository\Repository;

class PizzaIngredientRepository extends Repository
{
    public function getTableName(): string
    {
        return 'pizza_ingredient';
    }

    /**
     * methode qui permet de récuperé tous les ingredients d une pizza grace a sont id
     * @param int $pizza_id
     * @return array
     */
    public function getIngredientByPizzaId(int $pizza_id): array
    {
        //on déclareun tableau vide
        $array_result = [];
        //on crée notre requete SQL
        $q = sprintf(
            'SELECT *
            FROM %1$s AS pi
            INNER JOIN %2$s  AS i ON pi.`ingredient_id` = i.`id`
            WHERE pi.`pizza_id` = :id',
            $this->getTableName(), //corespond au %1$s
            AppRepoManager::getrm()->getIngredientRepository()->getTableName() //corespond au %2$s
        );

        //on prepare la requete
        $stmt = $this->pdo->prepare($q);
        //on verifie que la requete est bien preparer
        if (!$stmt) return $array_result;
        //si tous est bon on execute la requete
        $stmt->execute(['id' => $pizza_id]);
        //on peut recuperer les données de la requete
        while ($row_data = $stmt->fetch()) {
            // on crée l'objet Ingredient
            $array_result[] = new Ingredient($row_data);
        }
        //on retourne le tableau fraichement rempli
        return $array_result;
    }
}
