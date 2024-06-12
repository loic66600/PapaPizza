<?php

namespace App\Repository;

use App\Model\Size;
use Core\Repository\Repository;

class SizeRepository extends Repository
{
    public function getTableName(): string
    {
        return 'size';
    }

    /**  methode qui recupere toutes les tailles
     * 
    */
    public function getAllSize(): array
    {
        return $this->readAll(Size::class);
    }
}
