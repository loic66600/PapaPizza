<?php

namespace App;

use App\Repository\UserRepository;
use Core\Repository\RepositoryManagerTrait;

class AppRepoManager
{
//on recuper notre trait RepositoryManagerTrait
    use RepositoryManagerTrait;

    //on déclare une propriete privée qui contiendra l instance du ripository
    private UserRepository $userRepository;

//on crée le getter
    public function getUserRepository(): UserRepository
    {
       return $this->userRepository; 
    }
    //on declare un construct qui va instancier les ripository
    protected function __construct()
    {
        $config = App::getApp();
        $this->userRepository = new UserRepository($config);
    }

}