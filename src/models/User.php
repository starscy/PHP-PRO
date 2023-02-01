<?php

namespace Starscy\Project\models;

use  Starscy\Project\models\Person\Name;
use  Starscy\Project\models\UUID;

class User 
{
    public function __construct(
        private UUID $uuid,
        private string $username,
        private Name $fullname,
    )
    {
    }

    public function uuid():UUID
    {
        return $this->uuid;
    }

    public function setId($uuid):User
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getUsername():string
    {
        return $this->username;
    }

    public function setUsername($username):string
    {
        $this->username = $username;
        return $this;
    }


    public function getName():Name
    {
        return $this->fullname;
    }

    public function setName(string $name,string $secondName):User
    {
        $this->fullname = $name.$secondName;
        return $this;
    }

    public function __toString():string
    {
        return "$this->fullname .".PHP_EOL;
    }

}

