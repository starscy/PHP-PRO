<?php

namespace Starscy\Project\Http\Actions\Comment;

use Starscy\Project\Http\Auth\AuthException;
use Starscy\Project\Http\Auth\TokenAuthenticationInterface;
use Starscy\Project\models\Exceptions\InvalidArgumentException;
use Starscy\Project\Http\Actions\ActionInterface;
use Starscy\Project\Http\ErrorResponse;
use Starscy\Project\models\Exceptions\HttpException;
use Starscy\Project\Http\Request;
use Starscy\Project\Http\Response;
use Starscy\Project\Http\SuccessfulResponse;
use Starscy\Project\models\Blog\Post;
use Starscy\Project\models\Blog\Comment;
use Starscy\Project\models\Exceptions\UserNotFoundException;
use Starscy\Project\models\Exceptions\PostNotFoundException;
use Starscy\Project\models\Exceptions\CommentNotFoundException;
use Starscy\Project\models\Repositories\Comment\CommentRepositoryInterface;
use Starscy\Project\models\Repositories\Post\PostRepositoryInterface;
use Starscy\Project\models\UUID;

class CreateComment implements ActionInterface
{
    // Внедряем репозитории статей и пользователей

    public function __construct(

        private CommentRepositoryInterface $commentsRepository,
        private PostRepositoryInterface $postsRepository,
        private TokenAuthenticationInterface $authentication,

    ) {
    }

    public function handle(Request $request): Response
    {
//        var_dump($request->jsonBodyField('text'));
        try{
            $author = $this->authentication->user($request);
        } catch (AuthException $e){
            return new ErrorResponse($e->getMessage());
        }

        try {
            $postUuid = $request->JsonBodyField('post_uuid');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }


        try {
            $post=$this->postsRepository->get(new UUID("$postUuid"));
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $commentUuid = UUID::random();

        try {
            // Пытаемся создать объект статьи
            // из данных запроса

            $comment = new Comment(
                $commentUuid,
                $post,
                $author,
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        // Сохраняем новую статью в репозитории

        $this->commentsRepository->save($comment);

        // Возвращаем успешный ответ,
        // содержащий UUID новой статьи

        return new SuccessfulResponse([
            'uuid' => (string)$commentUuid,
        ]);
    }
}