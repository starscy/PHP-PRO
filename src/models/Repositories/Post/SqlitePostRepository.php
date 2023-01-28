<?php

namespace Starscy\Project\models\Repositories\Post;

use Starscy\Project\models\Blog\Post;
use Starscy\Project\models\Person\Name;
use Starscy\Project\models\UUID;
use PDO;
use Starscy\Project\models\Exceptions\PostNotFoundException;
use Starscy\Project\models\Repositories\Post\PostRepositoryInterface;

class SqlitePostRepository implements PostRepositoryInterface
{
    public function __construct (
       private PDO $pdo,
    )
    {
    }

    public function save(Post $post):void
    {

        $statement = $this->pdo->prepare(
        'INSERT INTO posts (uuid,author_uuid, title, text)
        VALUES (:uuid,:author_uuid, :title, :text)'
        );  

        $statement->execute([
        ':uuid' => (string)$post->uuid(),
        ':author_uuid'=> (string)$post->getUser()->uuid(),
        ':title' => $post->getTitle(),
        ':text' => $post->getText(),
        ]);
    }

    public function get(UUID $uuid):Post
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM posts WHERE uuid = :uuid'
        );   

        $statement->execute([
            ':uuid' => (string)$uuid
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);
        
        if ($result === false){
            throw new PostNotFoundException("user with id $uuid not found");
        }
        
        return new Post(
            new UUID($result['uuid']),
            $result['author_uuid'],
            new Name($result['title'], $result['text'])
            );
    }

}
