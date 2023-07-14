<?php
namespace Starscy\Project\Http\Auth;

use Starscy\Project\models\Exceptions\HttpException;
use Starscy\Project\models\Repositories\User\UserRepositoryInterface;
use Starscy\Project\Http\Request;
use Starscy\Project\models\User;

class PasswordAuthentication implements PasswordAuthenticationInterface
{
    public function __construct(
        private UserRepositoryInterface $usersRepository
    )
    {
    }

    public function user(Request $request): User
    {
            // 1. Идентифицируем пользователя
        try {
            $username = $request->jsonBodyField('username');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }
        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }
            // 2. Аутентифицируем пользователя
            // Проверяем, что предъявленный пароль
            // соответствует сохранённому в БД

        try {
            $password = $request->jsonBodyField('password');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        if (!$user->checkPassword($password)){
            throw new AuthException("Wrong password");
        }

        return $user;
    }
}