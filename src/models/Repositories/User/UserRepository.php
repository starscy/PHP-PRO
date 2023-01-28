<?php

namespace Starscy\Project\models\Repositories\User;

use Starscy\Project\models\User;
use Starscy\Project\models\Exceptions\UserNotFoundException;
use Starscy\Project\models\UUID;

class UserRepository  implements UserRepositoryInterface
{
    private array $users = [];

    // public function getAllUsers():array
    // {
    //     return $this->users;
    // }
    
    public function save(User $user):void
    {
        $this->users[] = $user; 
    }

    public function get(UUID $uuid):User
    {
        foreach($this->users as $user){
            if($user->uuid() === (string)$uuid){
                return $user;
            }

        }
        throw new UserNotFoundException("user with id $uuid not found");
    }

    public function getByUsername (string $username):User
    {
        foreach($this->users as $user){
            if($user->getUsername() === $username){
                return $user;
            }

        }
        throw new UserNotFoundException("user with id $uuid not found");
    }

}