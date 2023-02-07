<?php

namespace Starscy\Project\models\Repositories\Post;

use Starscy\Project\models\Blog\Post;
use Starscy\Project\models\Exceptions\PostNotFoundException;
use Starscy\Project\models\UUID;

class PostRepository  implements PostRepositoryInterface
{
    private array $posts = [];

    public function getAllPosts():array
    {
        return $this->posts;
    }
    
    public function save(Post $post):void
    {
        $this->posts[] = $post; 
    }

    public function get(UUID $uuid):Post
    {
        foreach($this->posts as $post){
            if($post->uuid() === (string)$uuid){
                return $post;
            }

        }
        throw new PostNotFoundException("post with id $uuid not found");
    }

    public function delete(UUID $uuid):void
    {
        
    }

}