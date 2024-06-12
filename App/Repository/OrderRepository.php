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
     * méthode qui permet de récupérer la dernière commande
     * @return ?int
     */
    public function findLastOrder(): ?int
    {
        $q = sprintf(
            'SELECT * 
            FROM `%s` 
            ORDER BY id DESC 
            LIMIT 1',
            $this->getTableName()
        );

        $stmt = $this->pdo->query($q);

        if (!$stmt) return null;

        $result = $stmt->fetchObject();

        return $result->id ?? 0;
    }

    /**
     * méthode qui retourne une commande si elle est dans le panier
     * @param int $user_id
     * @param string $status
     * @return bool
     */
    public function findLastStatusByUser(int $user_id, string $status): bool
    {
        //on crée la requete SQL
        $q = sprintf(
            'SELECT * 
            FROM `%s` 
            WHERE `user_id` = :user_id 
            AND `status` = :status 
            ORDER BY id DESC 
            LIMIT 1',
            $this->getTableName()
        );

        //on prépare la requete
        $stmt = $this->pdo->prepare($q);
        //on vérifie que la requete est bien executée
        if (!$stmt->execute(['user_id' => $user_id, 'status' => $status])) return false;

        //on récupère les résultats
        $result = $stmt->fetchObject();

        //si pas de resultat on retourne false
        if (!$result) return false;

        //si on a des résultats, on vérifie si la commande contient des lignes
        $count_row = $this->countOrderRow($result->id);
        //si on a pas de résultat on renvoi false
        if (!$count_row) return false;

        //si on a des résultats on renvoi true
        return true;
    }

    /**
     * méthode qui retourne le nombre de ligne de commande
     * @param int $order_id
     * @return ?int
     */
    public function countOrderRow(int $order_id): ?int
    {
        //query qui additionne le nombre de ligne de commande
        $q = sprintf(
            'SELECT SUM(quantity) as count
            FROM `%s` 
            WHERE `order_id` = :order_id',
            AppRepoManager::getRm()->getOrderRowRepository()->getTableName()
        );

        //on prépare la requete
        $stmt = $this->pdo->prepare($q);

        //on vérifie que la requete est bien executée
        if (!$stmt->execute(['order_id' => $order_id])) return 0;

        //on récupère le résultat
        $result = $stmt->fetchObject();
        //si pas de résultat on retourne 0 sinon le nombre de ligne
        if (!$result || is_null($result)) return 0;
        return $result->count;
    }

    /**
     * méthode qui permet de créer une commande
     * @param array $data
     * @return ?int 
     */
    public function insertOrder(array $data): ?int
    {
        //on crée la requete SQL
        $q = sprintf(
            'INSERT INTO `%s` (`order_number`, `date_order`, `status`, `user_id`) 
            VALUES (:order_number, :date_order, :status, :user_id)',
            $this->getTableName()
        );

        //on prépare la requete
        $stmt = $this->pdo->prepare($q);

        //si la requete n'est pas executée on retourne null
        if (!$stmt->execute($data)) return null;

        //on retourne l'id de la commande
        return $this->pdo->lastInsertId();
    }

    /**
     * méthode qui retourne l'id de la commande si status = IN_CART pour un utilisateur
     * @param int $user_id
     * @return ?int
     */
    public function findOrderIdByStatus(int $user_id): ?int
    {
        $status = Order::IN_CART;

        //on cree la requete SQL
        $q = sprintf(
            'SELECT * 
            FROM `%s` 
            WHERE `user_id` = :user_id 
            AND `status` = :status 
            ORDER BY id DESC 
            LIMIT 1',
            $this->getTableName()
        );

        //on prépare la requete
        $stmt = $this->pdo->prepare($q);

        //on vérifie que la requete est bien executée
        if (!$stmt->execute(['user_id' => $user_id, 'status' => $status])) return null;

        //on récupère les résultats
        $result = $stmt->fetchObject();

        //si on n'a pas de résultat on retourne null
        if (!$result) return null;

        //on retourne l'id de la commande
        return $result->id;
    }

    /**
     * méthode qui récupère la commande en cours d'un utilisateur avec les lignes de commande
     * @param int $user_id
     * @return ?object
     */
    public function findOrderInProgressWithOrderRow(int $user_id): ?object
    {
        //on crée la requete SQL
        $q = sprintf(
            'SELECT * 
            FROM `%s` 
            WHERE user_id = :user_id 
            AND status = :status',
            $this->getTableName()
        );

        //on prépare la requete
        $stmt = $this->pdo->prepare($q);

        //on execute la requete
        if (!$stmt->execute(['user_id' => $user_id, 'status' => Order::IN_CART])) return null;

        //on recupère le resultat
        $result = $stmt->fetchObject();

        //si pas de résultat on retourne null
        if (!$result) return null;

        //on doit hydrater notre objet Order avec toutes ses lignes de commandes associées
        $result->order_rows = AppRepoManager::getRm()->getOrderRowRepository()->findOrderRowByOrder($result->id);

        return $result;
    }

    /**
     * méthode qui permet de supprimer une commande
     * @param int $id
     * @return bool
     */
    public function deleteOrder(int $id): bool
    {
        //on cree la requete SQL
        $q = sprintf(
            'DELETE FROM `%s` 
            WHERE `id` = :id',
            $this->getTableName()
        );

        //on prepare la requete
        $stmt = $this->pdo->prepare($q);

        //on verifie que la requete est bien préparée
        if (!$stmt) return false;

        return $stmt->execute(['id' => $id]);
    }

    /**
     * methode qui récupere une commande avec sont id et les lignes de commande
     * @param int order_id
     * @return ?object
     */
    public function findOrderByIdWithRow(int $order_id): ?object
    {
        //on crée la requete SQL
        $q = sprintf(
            'SELECT * 
            FROM `%s` 
            WHERE `id` = :order_id',
            $this->getTableName()
        );

        //on prépare la requete
        $stmt = $this->pdo->prepare($q);

        //on execute la requete
        if (!$stmt->execute(['order_id' => $order_id])) return null;

        //on recupère le resultat
        $result = $stmt->fetchObject();

        //on va hydrater notre objet Order avec toutes ses lignes de commandes associées
        $result->order_rows = AppRepoManager::getRm()->getOrderRowRepository()->findOrderRowByOrder($result->id);

        return $result;
    }

    /**
     * methode qui permet de modifier une commande
     * @param array $data
     * @return bool
     */
    public function updateOrder(array $data): bool
    {
        //on crée la requete SQL
        $q = sprintf(
            'UPDATE `%s` 
            SET `status` = :status
            WHERE `id` = :id',
            $this->getTableName()
        );

        //on prepare la requete
        $stmt = $this->pdo->prepare($q);

        return $stmt->execute($data);
    }

    /**
     * methode qui permet de recupere toutes les commande
     * @param int $id
     * @return array
     * 
     */
    public function findOrderByUser(int $id): array
    {
        //on declare un tableau vide
        $result = [];
        //on crée la requete SQL
        $q = sprintf(
            'SELECT * 
            FROM `%s` 
            WHERE `user_id` = :id
            ORDER BY status ASC',
            $this->getTableName()
        );
        //on prepare la requete
        $stmt = $this->pdo->prepare($q);

        //on execute la requete
        if (!$stmt->execute(['id' => $id])) return $result;

        //on recupère le resultat
        while ($row_data = $stmt->fetch()) {

            $order = new Order($row_data);
            //on va hydrater notre objet Order avec toutes ses lignes de commandes associées
            $order->order_rows = AppRepoManager::getRm()->getOrderRowRepository()->findOrderRowByOrder($order->id);

            //on remplissage du tableau
            $result[$order->status][] = $order;
        }

        return $result;
    }


}
