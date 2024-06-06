<?php

namespace App\Model;

use Core\Model\Model;

class Pizza extends Model
{
    public string $name;
    public string $image_path;
    public bool $is_active ;  
    public int $user_id ;

    //propriete pour stocker l utilisateur
    public User $user;

    public array $ingredients = [];
    public array $prices = [];
    
}
