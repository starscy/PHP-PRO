<?php

namespace Starscy\Project\models;

use Starscy\Project\models\Exceptions\InvalidArgumentException;

Class UUID 
{
    public function __construct(
        private string $uuid
    )
    {
        if(!uuid_is_valid($uuid)){
            throw new InvalidArgumentException(
                "Malformed UUID:$this->uuid"
            );
        }
    }

    public static function random(): self
    {
        return new self(uuid_create(UUID_TYPE_RANDOM));
    }
 
    public function __toString():string
    {
        return $this->uuid;
    }

}
