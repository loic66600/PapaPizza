<?php

namespace App\Repository;

use App\Model\User;
use Core\Repository\Repository;

class UserRepository extends Repository
{
  public function getTableName(): string
  {
    return 'users';
  }

  /**
   * methode pour ajouter un user
   */
  public function addUser(array $data): ?User
  {
    //on cree un tableau vide pour que le client ne sois pas admin et soit actif
    $data_more = [
      'is_admin' => 0,
      'is_active' => 1
    ];
    //ici onfusion les tableau
    $data = array_merge($data, $data_more);
    //on peur cree la requete
    $query = sprintf('INSERT INTO %s (
       `email`,
        `firstname`,
        `lastname`,
        `phone`,
        `password`,
        `is_admin`,
        `is_active`,
         ) 
         VALUES (
             :email,
             :firstname,
             :lastname,
             :phone,
             :password,
             :is_admin,
             :is_active
         )', $this->getTableName());

        //on prepare la requete
        $stmt = $this->pdo->prepare($query);
        //on verifie que la requete est bien preparer
        if (!$stmt) return null;
        //si tous est bon on bind les valeurs
        $stmt->execute($data);
        //on peut recuperer les données id de l utilisateur fraichement crée
       $id = $this->pdo->lastInsertId();
//on peut retourner l objet User grace a sont id
        return $this-> readById(User::class, $id);


  }


}
