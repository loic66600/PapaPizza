<?php

namespace App\Repository;

use App\AppRepoManager;
use App\Model\Order;
use Core\Repository\Repository;

class OrderRepository extends Repository
{
    public function getTableName(): string
    {
        return 'order';
    }

    /**
     * methode qui permet de recuperer la derrnière commande
     */
    public function findLastOrder(): ?int
    {
        $q = sprintf(
            'SELECT * FROM `%s`
             ORDER BY id 
             DESC LIMIT 1',
            $this->getTableName()
        );

        $stmt = $this->pdo->query($q);
        if (!$stmt) return null;
        $result = $stmt->fetchObject();
        return $result->id ?? 0;
    }

    /**
     * methode qui retourne une commande si est dans le panier
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function findLastStatusByUser(int $user_id, string $status): bool
    {
        //on crée notre requete SQL
        $q = sprintf(
            'SELECT * 
            FROM `%s`
            WHERE user_id = :user_id
            AND status = :status
            ORDER BY id DESC
            LIMIT 1',
            $this->getTableName()
        );
        //on prepare la requete
        $stmt = $this->pdo->prepare($q);
        //on execute la requete
        if (!$stmt->execute(['user_id' => $user_id, 'status' => $status])) return false;

        //on retourne le resultat
        $result = $stmt->fetchObject();
        //si pas de resultat on retourne false
        if (!$result) return false;
        //si on des resultas on verifie si la commande contient des lignes
        $count_row = $this->countOrderRow($result->id);
        //si on pas de resultat on retourne false
        if (!$count_row) return false;
        //si on a des résultats on retourne true
        return true;
    }
    /**
     * methode qui permet de retouner le nombre de lignes d'une commande
     * @param int $id
     * @return int
     */
    public function countOrderRow(int $order_id): int
    {
        //query quii additionne 
        $q = sprintf(
            'SELECT SUM(quantity) AS count
        FROM `%s`
        WHERE order_id = :order_id',
            AppRepoManager::getrm()->getOrderRowRepository()->getTableName()

        );
        //on prepare la requete
        $stmt = $this->pdo->prepare($q);
        //on execute la requete
        if (!$stmt->execute(['order_id' => $order_id])) return 0;

        //on retourne le resultat
        $result = $stmt->fetchObject();
        //si pas de resultat on retourne false
        if (!$result || is_null($result)) return 0;
        return $result->count;
    }

    /**
     * methode quiperment de crée une commande
     * @param array $data
     * @return ?int
     */
    public function insertOrder(array $data): ?int
    {
        //on cree la requete sql
        $q = sprintf(

            'INSERT INTO `%s`(`user_id`, `status`, `order_number`, `date_order`)
       
         VALUES(:user_id, :status, :order_number, :date_order)',
            $this->getTableName()
        );

        //on prepare la requete
        $stmt = $this->pdo->prepare($q);

        //si la requete n'est pas preparee on retourne null
        if (!$stmt->execute($data)) return null;
        //on retourne l'id de commande
        return $this->pdo->lastInsertId();
    }

    /**
     * methode qui retourne l id d'une commande par son status = in_cart
     * @param int $user_id
     * return ?int
     */
    public function findOrderIdByStatus(int $user_id): ?int
    {
        $status = Order::IN_CART;
        $q = sprintf(
            'SELECT *
            FROM `%s`
            WHERE user_id = :user_id
            AND status = :status
            ORDER BY id DESC
            LIMIT 1',
            $this->getTableName()
        );
        //on prepare la requete
        $stmt = $this->pdo->prepare($q);
        //on execute la requete
        if (!$stmt->execute(['user_id' => $user_id, 'status' => $status])) return null;
        //on retourne le resultat
        $result = $stmt->fetchObject();
        //si pas de resultat on retourne null
        if (!$result) return null;
        //on retourne l'id de commande
        return $result->id;
    }
}
