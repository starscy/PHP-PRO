<?php

namespace Starscy\Project\Http\Actions\User;

use Starscy\Project\Http\Actions\ActionInterface;
use Starscy\Project\Http\ErrorResponse;
use Starscy\Project\models\Exceptions\HttpException;
use Starscy\Project\Http\Request;
use Starscy\Project\Http\Response;
use Starscy\Project\Http\SuccessfulResponse;
use Starscy\Project\models\Exceptions\UserNotFoundException;
use Starscy\Project\models\Repositories\User\UserRepositoryInterface;

// Класс реализует контракт действия

class FindByUsername implements ActionInterface
{

    // Нам понадобится репозиторий пользователей,
    // внедряем его контракт в качестве зависимости

    public function __construct(
        private UserRepositoryInterface $userRepository
    ) 
    {
    }

    // Функция, описанная в контракте

    public function handle(Request $request): Response
    {
        try {

            // Пытаемся получить искомое имя пользователя из запроса

            $username = $request->query('username');
        } catch (HttpException $e) {

            // Если в запросе нет параметра username -
            // возвращаем неуспешный ответ,
            // сообщение об ошибке берём из описания исключения

            return new ErrorResponse($e->getMessage());
        }
        
        try {

    // Пытаемся найти пользователя в репозитории

        $user = $this->userRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {

    // Если пользователь не найден -
    // возвращаем неуспешный ответ

        return new ErrorResponse($e->getMessage());
    }

    // Возвращаем успешный ответ

    return new SuccessfulResponse([
        'username' => $user->getUsername(),
        'name' => $user->getName()->getFirst() . ' ' . $user->getName()->getSecond(),
    ]);
    }
}