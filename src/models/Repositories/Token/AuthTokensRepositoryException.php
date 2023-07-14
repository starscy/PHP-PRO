<?php

namespace Starscy\Project\models\Repositories\Token;

use Starscy\Project\models\Exceptions\AppException;

class AuthTokensRepositoryException extends AppException
{

    /**
     * @param string $getMessage
     * @param int $param
     * @param \Exception|PDOException $e
     */
    public function __construct(string $getMessage, int $param, $e)
    {
    }
}