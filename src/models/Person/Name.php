<?php

namespace Starscy\Project\models\Person;

class Name
{
    public function __construct(
        private string $firstName,
        private string $secondName
    )
    {}
    public function __toString():string
    {
        return "$this->firstName "." "." $this->secondName ";
    }

}