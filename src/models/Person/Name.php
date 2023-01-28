<?php

namespace Starscy\Project\models\Person;

class Name
{
    public function __construct(
        private string $firstName,
        private string $secondName
    )
    {}

    public function getFirst():string
    {
        return $this->firstName;
    }

    public function setFirst($firstName):string
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getSecond():string
    {
        return $this->secondName;
    }

    public function setSecond($secondName):string
    {
        $this->secondName = $secondName;
        return $this;
    }

    public function __toString():string
    {
        return "$this->firstName "." "." $this->secondName ";
    }

}