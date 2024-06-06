<?php

namespace App\Repository;

use App\Model\Size;
use App\Model\Price;
use App\AppRepoManager;
use Core\Repository\Repository;

class PriceRepository extends Repository
{
    public function getTableName(): string
    {
        return 'price';
    }
    /**
     * methode qui permet de récuperé tous les prix d'une pizza grace a sont id avec sa taille associée
     * @param int $pizza_id
     * @return array
     */
    public function getPriceByPizzaId(int $pizza_id): array
    {
        //on déclareun tableau vide
        $array_result = [];
        //on crée notre requete SQL
        $q = sprintf(
            'SELECT p.*, s.`label`
            FROM %1$s AS p
            INNER JOIN %2$s  AS s ON p.`size_id` = s.`id`
            WHERE p.`pizza_id` = :id',
            $this->getTableName(), //corespond au %1$s
            AppRepoManager::getrm()->getSizeRepository()->getTableName() //corespond au %2$s
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
           $price = new Price($row_data);
           //on va construir a la main le tableau pour creé une instrance de size
           $size_data = [
               'id'=> $row_data['size_id'],
               'label'=> $row_data['label']
           ];
           //on peut maintenant instancier l'objet size
           $size= new Size($size_data);
           //on va hydrater l'objet price ave size
           $price->size = $size;

           //on remplis le tableau avec l'objet price
           $array_result[] = $price;
          
        }
        //on retourne le tableau fraichement rempli
        return $array_result;
    }
}
