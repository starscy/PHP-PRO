<?php

namespace Starscy\Project\Http\Actions\Likes;

use Starscy\Project\models\Exceptions\InvalidArgumentException;
use Starscy\Project\Http\Actions\ActionInterface;
use Starscy\Project\Http\ErrorResponse;
use Starscy\Project\models\Exceptions\HttpException;
use Starscy\Project\Http\Request;
use Starscy\Project\Http\Response;
use Starscy\Project\Http\SuccessfulResponse;
use Starscy\Project\models\Blog\Post;
use Starscy\Project\models\Blog\Like;
use Starscy\Project\models\Repositories\Post\PostRepositoryInterface;
use Starscy\Project\models\Exceptions\UserNotFoundException;
use Starscy\Project\models\Repositories\User\UserRepositoryInterface;
use Starscy\Project\models\Repositories\Likes\LikesRepositoryInterface;
use Starscy\Project\models\UUID;

class CreatePostLike implements ActionInterface
{
    // Внедряем репозитории статей и пользователей

    public function __construct(

        private LikesRepositoryInterface $likesRepository,
        private PostRepositoryInterface $postsRepository,
        private UserRepositoryInterface $usersRepository,


    ) {
    }

    public function handle(Request $request): Response
    {
        // Пытаемся создать UUID пользователя из данных запроса

        try {
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
            
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        try {
            $user= $this->usersRepository->get($authorUuid);
            $post= $this->postsRepository->get($postUuid);

        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        $likeUuid = UUID::random();

        try {
            $like = new Like(
                $likeUuid,
                $post,
                $user
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->likesRepository->checkIfPostBeenLikedByUser($postUuid,$authorUuid);

        $this->likesRepository->save($like);

        return new SuccessfulResponse([
            'uuid' => (string)$likeUuid,
        ]);
    }
}