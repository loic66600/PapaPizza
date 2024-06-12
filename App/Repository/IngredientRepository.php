<?php

namespace App\Repository;

use App\Model\Ingredient;
use Core\Repository\Repository;

class IngredientRepository extends Repository
{
    public function getTableName(): string
    {
        return 'ingredient';
    }


    /**
     * methode qui récupere tous les ingredients actif par categorie
     */
    public function getIngredientActiveByCategory(): array
    {
        $array_result = [];

        $q = sprintf(
            'SELECT * 
        FROM `%s` 
        WHERE `is_active` =1  
        ORDER BY `category` ASC',
            $this->getTableName()
        );
        //on execute la requete
        $stmt = $this->pdo->query($q);
        //on execute
        if (!$stmt) return $array_result;
        //on retourne les resultats
        while ($row_data = $stmt->fetch()) {
            $array_result[$row_data['category']][] = new Ingredient($row_data);
        }

        return $array_result;
    }
}
