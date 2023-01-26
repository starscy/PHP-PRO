<?php

namespace Starscy\Project\models\Repositories;

use Starscy\Project\models\User;
use Starscy\Project\models\Exceptions\UserNotFoundException;

Class UserRepository 
{
    private array $users = [];

    public function getAllUsers()
    {
        return $this->users;
    }
    
    public function saveUser(User $user):void
    {
        $this->users[] = $user; 
    }

    public function getUser (string $id):User
    {
        foreach($this->users as $user){
            if($user->getId() === $id){
                return $user;
            }

        }
        throw new UserNotFoundException("user with id $id not found");
    }


}