<?php
namespace Starscy\Project\models\Repositories\Token;

use Starscy\Project\models\AuthToken;

interface AuthTokensRepositoryInterface
{
// Метод сохранения токена
    public function save(AuthToken $authToken): void;
// Метод получения токена
    public function get(string $token): AuthToken;
}