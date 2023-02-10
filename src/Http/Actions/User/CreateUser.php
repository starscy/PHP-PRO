<?php

namespace Starscy\Project\Http\Actions\User;

use Starscy\Project\Http\Actions;
use Starscy\Project\models\Repositories\User\UserRepositoryInterface;
use Starscy\Project\Http\Request;
use Starscy\Project\Http\Response;
use Starscy\Project\Http\SuccessfulResponse;
use Starscy\Project\Http\Actions\ActionInterface;
use Starscy\Project\models\UUID;
use Starscy\Project\models\Person\Name;
use Starscy\Project\models\User;
use Starscy\Project\models\Exceptions\InvalidArgumentException;
use Starscy\Project\models\Exceptions\HttpException;
use Starscy\Project\Http\ErrorResponse;

class CreateUser implements ActionInterface
{
    public function __construct(
        private UserRepositoryInterface  $userRepository
    ) 
    {
    }

    public function handle(Request $request): Response
    {

        try {
            $userUuid = UUID::random();

            $user = new User(
                $userUuid,
                $request->jsonBodyField('username'),
                new Name (
                    $request->jsonBodyField('first_name'),
                    $request->jsonBodyField('second_name')
                )
            );

        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->userRepository->save($user);

        return new SuccessfulResponse([
            'uuid' => (string)$userUuid,
        ]);
    }
}