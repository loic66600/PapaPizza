<?php

namespace Core\Repository;

trait RepositoryManagerTrait
{
    /**
     * un trait permet de gérer une portion de code directement dans une classe 
     * sans  notion de  hierarchie
     * dans ce trait on va pouvoir utiliser la notion de self qui fera référence à la classe qui utilise le trait
     * ici on aura un disigne de singleton
     */
    //on cree une propriete privée qui contiendra l instance de la classe
    private static ?self $rm_instance = null;


    public static function getrm(): self
    {
        if (is_null(self::$rm_instance)) {
            self::$rm_instance = new self();
        }
        return self::$rm_instance;
    }

    protected function __construct()
    {
    }
    protected function __clone()
    {
    }
}
