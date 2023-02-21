<?php

namespace Starscy\Project\models\Repositories\Comment;

use Psr\Log\LoggerInterface;
use Starscy\Project\models\Blog\Comment;
use Starscy\Project\models\UUID;
use PDO;
use Starscy\Project\models\Exceptions\CommentNotFoundException;
use Starscy\Project\models\Repositories\Post\SqlitePostRepository;
use Starscy\Project\models\Repositories\User\SqliteUserRepository;

class SqliteCommentRepository implements CommentRepositoryInterface
{
    public function __construct (
       private PDO $pdo,
       private LoggerInterface $logger,
    )
    {
    }

    public function save(Comment $comment):void
    {

        $statement = $this->pdo->prepare(
        'INSERT INTO comments (uuid, post_uuid, author_uuid,text)
        VALUES (:uuid, :post_uuid, :author_uuid, :text)'
        );  

        $statement->execute([
            ':uuid' => (string)$comment->uuid(),
            ':post_uuid' => (string)$comment->getPost()->uuid(),
            ':author_uuid'=> (string)$comment->getUser()->uuid(),
            ':text' => $comment->getText(),
        ]);

        $this->logger->info("Comment created:". $comment->getText());
    }

    public function get(UUID $uuid):Comment
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM comments WHERE uuid = :uuid'
        );   

        $statement->execute([
            ':uuid' => (string)$uuid
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false){
            throw new CommentNotFoundException("Cannot find Comment: $uuid");
        }

        $postRepository = new SqlitePostRepository($this->pdo);
        $post = $postRepository->get(new UUID($result['post_uuid'])) ;

        $userRepository = new SqliteUserRepository($this->pdo);
        $user = $userRepository->get(
            new UUID($result['author_uuid'])
        ) ;
        
        return new Comment(
            new UUID($result['uuid']),
            $post,
            $user,
            $result['text']
        );
    }

}
