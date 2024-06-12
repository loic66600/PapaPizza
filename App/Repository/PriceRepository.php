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
     * méthode qui permet de récupérer tous les prix d'une pizza grace à son id avec sa taille associée
     * @param int $pizza_id
     * @return array
     */
    public function getPriceByPizzaId(int $pizza_id):array 
    {
        //on déclare un tableau vide
        $array_result = [];
        //on crée la requete SQL
        $q = sprintf(
            'SELECT p.*, s.`label` 
            FROM %1$s AS p 
            INNER JOIN %2$s AS s ON p.`size_id` = s.`id`
            WHERE p.`pizza_id` = :id',
            $this->getTableName(), //correspond au %1$s
            AppRepoManager::getRm()->getSizeRepository()->getTableName() //correspond au %2$s
        );

        //on prépare la requete
        $stmt = $this->pdo->prepare($q);

        //on vérifie que la requete est bien executée
        if (!$stmt) return $array_result;

        //on execute la requete en passant l'id de la pizza
        $stmt->execute(['id' => $pizza_id]);

        //on récupère les résultats
        while ($row_data = $stmt->fetch()) {
            //a chaque passage de la boucle on instancie un objet ingredient
            $price = new Price($row_data);

            //on va reconstruire à la main un tableau pour crée une instance de Size
            $size_data = [
                'id' => $row_data['size_id'],
                'label' => $row_data['label']
            ];

            //on peut maintenant instancier un objet Size
            $size = new Size($size_data);

            //on va hydrater Price avec Size
            $price->size = $size;

            //on rempli le tableau avec l'objet Price
            $array_result[] = $price;
        }

        //on retourne le tableau fraichement rempli
        return $array_result;
    }
    /**
     * méthode qui permet de récupérer le prix d'une pizza grace à son id avec sa taille associée
     * @param int $pizza_id
     * @param int $size_id
     * @return ?object
     */
    public function getPriceByPizzaIdBySize(int $pizza_id, int $size_id):?float
    {
        
        //on crée la requete SQL
        $q = sprintf(
            'SELECT p.*, s.`label` 
            FROM %1$s AS p 
            INNER JOIN %2$s AS s ON p.`size_id` = s.`id`
            WHERE p.`pizza_id` = :id 
            AND p.`size_id` = :size_id',
            $this->getTableName(), //correspond au %1$s
            AppRepoManager::getRm()->getSizeRepository()->getTableName() //correspond au %2$s
        );

        //on prépare la requete
        $stmt = $this->pdo->prepare($q);

        //on vérifie que la requete est bien executée
        if (!$stmt) return null;

        //on execute la requete en passant l'id de la pizza
        $stmt->execute(['id' => $pizza_id, 'size_id' => $size_id]);

        //on récupère le résultat
        $result = $stmt->fetchObject();

        //on vérifie si on a un résultat
        if(!$result) return null;

        //on retourne le prix
        return $result->price;
    }

    /**
    * methode qui permet d ajouter des ingredients à une pizza
    * @param array $data
    * @return bool
    * 
    */
   public function insertprice(array $data): bool
   {
     //on crée la requête SQL
     $q = sprintf(
       'INSERT INTO %s
       (`price`, `size_id`, `pizza_id`)
       VALUES
       (:price, :size_id, :pizza_id)',
       $this->getTableName()
     );
 
     //on prépare la requête
     $stmt = $this->pdo->prepare($q);
 
     //on verifie que la requête est bien préparée
     if (!$stmt) return false;
 
     //on execute la requête en passant les paramètres
     $stmt->execute($data);
 
     //on regarde si on a au moins une ligne qui a ete inseré
     return $stmt->rowCount() > 0;
   }
 
}
