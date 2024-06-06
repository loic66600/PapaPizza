<?php

namespace Core\Database;

use PDO;
//singleton pattern

class Database
{
    //on crée une constante avec option de pdo
    //ici on veut qu'une seule instance de PDO sois retourné sous  forme de tableau asssociatif
    private const PDO_OPTIONS = [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];
    //on crée une propriété et de statique pour stocker l'instance de pdo
    private static ?PDO $pdoInstance = null;

//on crée une methode statique en public pour recuperer l'instance de pdo
    public static function getPDO(DatabaseConfigInterface $config): PDO
    {
        if (is_null(self::$pdoInstance)) {
            $dsn = sprintf('mysql:dbname=%s;host=%s', $config->getName(), $config->getHost());
            self::$pdoInstance = new PDO(
                $dsn,
                $config->getUser(),
                $config->getPass(),
                self::PDO_OPTIONS
            );
        }
        //on retooune l'instance de pdo
        return self::$pdoInstance;
    }
    //le construc en private pour empeche l'instanciation de la class
    private function __construct() {}
    //le clone en private pour empeche le clonnage de la class
    private function __clone() {}


}
