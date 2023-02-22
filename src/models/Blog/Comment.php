<?php
namespace  Starscy\Project\models\Blog;

use Starscy\Project\models\User;
use  Starscy\Project\models\UUID;
use Starscy\Project\models\Blog\Post;

class Comment 
{
    public function __construct(
        private UUID $uuid,
        private Post $post,
        private User $user,
        private string $text
    )
    {
    }

    public function uuid():UUID
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid):Comment
    {
        $this->uuid = $uuid;
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

    public function getPost():Post
    {
        return $this->post;
    }

    public function setPost($post):Post
    {
        $this->post = $post;
        return $this;
    }

    public function getText():string
    {
        return $this->text;
    }

    public function setText(string $text):Comment
    {
        $this->text=$text;
        return this;
    }

    public function __toString():string
    {
        return "$this->user \n  $this->post \n $this->text .".PHP_EOL;
    }


}