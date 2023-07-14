<?php
namespace Starscy\Project\Http\Auth;

use Starscy\Project\Http\Auth\IdentificationInterface;
use Starscy\Project\models\Repositories\User\UserRepositoryInterface;
use Starscy\Project\Http\Request;
use Starscy\Project\models\User;
use Starscy\Project\models\UUID;
use Starscy\Project\models\Exceptions\HttpException;
use Starscy\Project\models\Exceptions\InvalidArgumentException;
use Starscy\Project\models\Exceptions\UserNotFoundException;


class JsonBodyUuidIdentification implements TokenAuthenticationInterface
{
    public function __construct(
        private UserRepositoryInterface $usersRepository
    ) 
    {
    }

    public function user(Request $request): User
    {
        try {
            // Получаем UUID пользователя из JSON-тела запроса;
            // ожидаем, что корректный UUID находится в поле user_uuid

            $userUuid = new UUID($request->jsonBodyField('user_uuid'));
        } catch (HttpException|InvalidArgumentException $e) {

            // Если невозможно получить UUID из запроса -
            // бросаем исключение

            throw new AuthException($e->getMessage());
        }
        try {
            // Ищем пользователя в репозитории и возвращаем его

            return $this->usersRepository->get($userUuid);
        } catch (UserNotFoundException $e) {

            // Если пользователь с таким UUID не найден -
            // бросаем исключение

            throw new AuthException($e->getMessage());
        }
    }
}