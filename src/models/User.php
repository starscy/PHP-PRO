<?php

namespace Starscy\Project\models;

use  Starscy\Project\models\Person\Name;

class User 
{
    public function __construct(
        private int $id,
        private Name $fullname,
    )
    {
    }

    public function getId():string
    {
        return $this->id;
    }

    public function setId($id):User
    {
        $this->id = $id;
        return $this;
    }

    public function getName():string
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

