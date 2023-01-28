<?php

namespace Starscy\Project\models\Repositories\Comment;

use Starscy\Project\models\Blog\Comment;
use Starscy\Project\models\Exceptions\CommentNotFoundException;
use Starscy\Project\models\UUID;

class CommentRepository  implements CommentRepositoryInterface
{
    private array $Comments = [];

    public function getAllComments():array
    {
        return $this->Comments;
    }
    
    public function save(Comment $Comment):void
    {
        $this->Comments[] = $Comment; 
    }

    public function get(UUID $uuid):Comment
    {
        foreach($this->Comments as $comment){
            if($comment->uuid() === (string)$uuid){
                return $comment;
            }

        }
        throw new CommentNotFoundException("Comment with id $uuid not found");
    }

}