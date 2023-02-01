<?php

namespace Starscy\Project\models\Repositories\Post;

use Starscy\Project\models\Blog\Post;
use Starscy\Project\models\Person\Name;
use Starscy\Project\models\UUID;
use PDO;
use Starscy\Project\models\Exceptions\PostNotFoundException;
use Starscy\Project\models\Repositories\Post\PostRepositoryInterface;
use Starscy\Project\models\Repositories\User\SqliteUserRepository;

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
            throw new PostNotFoundException("Cannot find post: $uuid");
        }

        $userRepository = new SqliteUserRepository($this->pdo);
        $user = $userRepository->get(new UUID($result['author_uuid'])) ;

        return new Post(
            new UUID($result['uuid']),
            $user,
            $result['title'], 
            $result['text']
        );
    }

}
