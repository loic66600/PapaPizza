<?php

namespace App\Repository;

use Core\Repository\Repository;

class OrderRowRepository extends Repository
{
    public function getTableName(): string
    {
        return 'order_row';
    }

    /**
     * methode qui permet de recuperer le dernier row d'une commande
     * @param array $data
     * @return bool
     */
    public function insertOrderRow(array $data): bool
    {
        //on cree la requete sql
        $q = sprintf(

        'INSERT INTO `%s`(`order_id`, `pizza_id`, `quantity`, `price`)
       
         VALUES (:order_id, :pizza_id, :quantity , :price)',
            $this->getTableName()
        );

        //on prepare la requete
        $stmt = $this->pdo->prepare($q);
        //on verifie que la requete est bien preparer
        if (!$stmt->execute($data)) return false;
        //on execute en passant les valeurs
  
        return true;
    }


}
