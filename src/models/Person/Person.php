<?php

namespace Starscy\Project\models\Person;

use Starscy\Project\models\person\Name;
use DateTimeImmutable;

class Person
{
    public function __construct(
        private Name $name,
        private DateTimeImmutable $registeredOn
    )
    {}
    public function __toString():string
    {
        return $this->name.' на сайте с '. $this->registeredOn->format('Y-m-d') .PHP_EOL;
    }

}