<?php
namespace  Starscy\Project\models\Blog;

use Starscy\Project\models\User;

class Comment 
{
    public function __construct(
        private int $id,
        private User $user,
        private Post $post,
        private string $text
    )
    {
    }

    public function getId():int
    {
        return $this->id;
    }

    public function setId($id):Comment
    {
        $this->id = $id;
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