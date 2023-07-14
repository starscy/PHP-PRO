<?php

namespace Starscy\Project\Http\Actions;

use HttpException;
use Starscy\Project\Http\Actions\ActionInterface;
use Starscy\Project\Http\ErrorResponse;
use Starscy\Project\Http\Request;
use Starscy\Project\Http\Response;
use Starscy\Project\Http\SuccessfulResponse;
use Starscy\Project\models\Exceptions\UserNotFoundException;
use Starscy\Project\models\Repositories\User\UserRepositoryInterface;

class IndexAction implements ActionInterface
{

    // Функция, описанная в контракте

    public function handle(Request $request): Response
    {
        return new SuccessfulResponse([
            'index' => 'mainPage',
        ]);
    }
}