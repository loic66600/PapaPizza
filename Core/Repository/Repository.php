<?php

namespace Core\Repository;

use PDO;
use Core\Database\Database;
use Core\Database\DatabaseConfigInterface;

abstract class Repository
{
    //on cree une propriété privée qui contiendra l instance de pdo

    protected PDO $pdo;

    abstract public function getTableName(): string;

    public function __construct(DatabaseConfigInterface $config)
    {
        $this->pdo = Database::getPDO($config);
    }

    //ici on peut definir les methodes  generique  pour les riposorties
    /**
     * methode qui récupere tous les élément de la table
     * ex: SELCT * FROM table
     * @param string $class_name
     * @return array
     */
    public function readAll(string $class_name)
    {
//on déclare un tableau vide
$array_result = [];
//on crée notre requete sql
$q = sprintf("SELECT * FROM %s", $this->getTableName());
//on execute la requete
$stmt = $this->pdo->query($q);
//si la requete n'est pas valide
if (!$stmt) return $array_result;
   //on peut recuperer les données de la requete
   while($row_data = $stmt->fetch()) {
       $array_result[] = new $class_name($row_data);
   }


return $array_result;
    }


        /**
     * methode qui récupere tous les élément de la table par son id
     * ex: SELCT * FROM table WHERE id = $id
     * @param string $class_name
     * @param int $id
     */
    public function readById(string $class_name, int $id):object
    {
        $q = sprintf("SELECT * FROM %s WHERE id = :id", $this->getTableName());
        //on prepare la requete
        $stmt = $this->pdo->prepare($q);
        //on verifie que la requete est bien preparer
        if (!$stmt) return null;
        //si tous est bon on bind les valeurs
        $stmt->execute(['id' => $id]);
        $row_data = $stmt->fetch();

        return!empty($row_data) ? new $class_name($row_data): null;

    }

}
