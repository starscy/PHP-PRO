<?php

namespace Starscy\Project\models\Repositories\Comment;

use Starscy\Project\models\Blog\Comment;
use Starscy\Project\models\UUID;
use PDO;
use Starscy\Project\models\Exceptions\CommentNotFoundException;

class SqliteCommentRepository implements CommentRepositoryInterface
{
    public function __construct (
       private PDO $pdo,
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
            throw new CommentNotFoundException("user with id $uuid not found");
        }
        
        return new Comment(
            new UUID($result['uuid']),
            $result['post_uuid'],
            $result['author_uuid'],
            $result['text']
        );
    }

}
