<?php

namespace Starscy\Project\models\Repositories\Token;


class AuthTokenNotFoundException extends AuthTokensRepositoryException
{

    /**
     * @param string $string
     */
    public function __construct(string $string)
    {
    }
}