<?php

namespace Starscy\Project\models\Repositories\Likes;

use Starscy\Project\models\Blog\Post;
use Starscy\Project\models\Blog\Like;
use Starscy\Project\models\UUID;
use PDO;
use Starscy\Project\models\Repositories\Likes\LikesRepositoryInterface;
use Starscy\Project\models\Exceptions\LikeAlreadyExist;

use Starscy\Project\models\Repositories\Post\SqlitePostRepository;
use Starscy\Project\models\Repositories\User\SqliteUserRepository;

class SqliteLikesRepository implements LikesRepositoryInterface
{
    public function __construct (
        private PDO $pdo,
    )
    {
    }

    public function save(Like $like):void
    {

        $statement = $this->pdo->prepare(
            'INSERT INTO likes (uuid, post_uuid, author_uuid)
            VALUES (:uuid, :post_uuid, :author_uuid)'
        );  

        $statement->execute([
            ':uuid' => (string)$like->uuid(),
            ':post_uuid' => (string)$like->getPost()->uuid(),
            ':author_uuid'=> (string)$like->getUser()->uuid(),
        ]);
    }

    public function getByPostUuid(UUID $uuid):array 
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM likes WHERE post_uuid = :post_uuid'
        );

        $statement->execute([
            ':post_uuid' => (string) $uuid
        ]);

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
              
        if ($result === false){
            throw new PostNotFoundException("Cannot find this post");
        }

        $likesRepository = new SqliteLikesRepository($this->pdo);

        // return $result;

        $likes=[];

        $userRepository = new SqliteUserRepository($this->pdo);

        $postRepository = new SqlitePostRepository($this->pdo);
        
        
        foreach($result as $key=>$value){         
            $user = $userRepository->get(new UUID($value['author_uuid'])) ;
            $post = $postRepository->get(new UUID($value['post_uuid'])) ;
            $likes[] = new Like(
                new UUID($value['uuid']),
                $post,
                $user,  
            );
        }
      
        return $likes;
      
    }

    public function checkIfPostBeenLikedByUser(UUID $postUuid,UUID $userUuid):void
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM likes WHERE post_uuid = :post_uuid AND author_uuid =:author_uuid'  
        );

        $statement->execute([
            ':post_uuid' => (string)$postUuid,
            ':author_uuid'=>(string)$userUuid,
        ]);

        $beenLiked = $statement->fetch();
        
        if($beenLiked){
            throw new LikeAlreadyExist(
                'User\'s like already exist'
            );
        }
    }

}