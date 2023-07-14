<?php

namespace Starscy\Project\Http\Actions\Auth;

use DateTimeImmutable;
use Starscy\Project\Http\Actions\ActionInterface;
use Starscy\Project\Http\Auth\AuthException;
use Starscy\Project\Http\Auth\PasswordAuthenticationInterface;
use Starscy\Project\models\AuthToken;
use Starscy\Project\models\Repositories\Token\AuthTokensRepositoryInterface;
use Starscy\Project\Http\Request;
use Starscy\Project\Http\ErrorResponse;
use Starscy\Project\Http\Response;
use Starscy\Project\Http\SuccessfulResponse;

class  LogIn implements  ActionInterface
{
    public function __construct(
// Авторизация по паролю
        private PasswordAuthenticationInterface $passwordAuthentication,
// Репозиторий токенов
        private AuthTokensRepositoryInterface $authTokensRepository
    ) {
    }
    public function handle(Request $request): Response
    {
// Аутентифицируем пользователя
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
// Генерируем токен
        $authToken = new AuthToken(
// Случайная строка длиной 40 символов
            bin2hex(random_bytes(40)),
            $user->uuid(),
// Срок годности - 1 день
            (new DateTimeImmutable())->modify('+1 day')
        );
// Сохраняем токен в репозиторий
        $this->authTokensRepository->save($authToken);
// Возвращаем токен
        return new SuccessfulResponse([
            'token' => (string)$authToken->token(),
        ]);
    }
}