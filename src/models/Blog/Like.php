<?php
namespace  Starscy\Project\models\Blog;

use Starscy\Project\models\User;
use Starscy\Project\models\Blog\Post;
use Starscy\Project\models\UUID;

class Like 
{
    public function __construct(
        private UUID $uuid,
        private Post $post,
        private User $user,
    )
    {
    }

    public function uuid():UUID
    {
        return $this->uuid;
    }

    public function setUuid($uuid):Post
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getPost():Post
    {
        return $this->post;
    }

    public function setPost($user):Post
    {
        $this->post=$post;
        return $this;
    }

    public function getUser():User
    {
        return $this->user;
    }

    public function setUser($user):User
    {
        $this->user=$user;
        return $this;
    }

    public function __toString():string
    {
        return "$this->post->getTitle() liked by $this->user->getUsername.".PHP_EOL;
    }

}