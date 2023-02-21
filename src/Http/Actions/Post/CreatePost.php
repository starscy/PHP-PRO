<?php

namespace Starscy\Project\Http\Actions\Post;

use Starscy\Project\Http\Auth\AuthenticationInterface;
use Starscy\Project\Http\Auth\AuthException;
use Starscy\Project\Http\Auth\TokenAuthenticationInterface;
use Starscy\Project\models\Exceptions\InvalidArgumentException;
use Starscy\Project\Http\Actions\ActionInterface;
use Starscy\Project\models\Exceptions\HttpException;
use Starscy\Project\Http\Request;
use Starscy\Project\Http\Response;
use Starscy\Project\Http\SuccessfulResponse;
use Starscy\Project\models\Blog\Post;
use Starscy\Project\models\Repositories\Post\PostRepositoryInterface;
use Starscy\Project\models\Exceptions\UserNotFoundException;
use Starscy\Project\models\Repositories\User\UserRepositoryInterface;
use Starscy\Project\models\UUID;
use Psr\Log\LoggerInterface;
use Starscy\Project\Http\ErrorResponse;

class CreatePost implements ActionInterface
{
    // Внедряем репозитории статей и пользователей

    public function __construct(

        private PostRepositoryInterface $postsRepository,
        private TokenAuthenticationInterface $authentication,
        private LoggerInterface $logger,

    ) {
    }

    public function handle(Request $request): Response
    {
       // Идентифицируем пользователя -
        // автора статьи
        try{
            $author = $this->authentication->user($request);
        } catch (AuthException $e){
            return new ErrorResponse($e->getMessage());
        }


        // Генерируем UUID для новой статьи
            
        $newPostUuid = UUID::random();

        try {
            // Пытаемся создать объект статьи
            // из данных запроса

            $post = new Post(
                $newPostUuid,
                // $authorUuid,
                $author,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        $this->postsRepository->save($post);

        // Логируем UUID новой статьи

        $this->logger->info("Post created: $newPostUuid");

        // Возвращаем успешный ответ,
        // содержащий UUID новой статьи

        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }
}