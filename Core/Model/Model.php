<?php

namespace Core\Model;

class Model
{
    public int $id;

    public function __construct(array $data_row =[]){
        //si on a des donnée, on les injecte dans l objet
        foreach ($data_row as $column => $value) {
            //si la proiete n existe pas on va a la suivants
            if (!property_exists($this, $column)) continue;
            //sinon on injecte la valeur dans la proprieté
            $this->{$column} = $value;
        }
    }
}