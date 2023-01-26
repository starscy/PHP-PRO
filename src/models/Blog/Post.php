<?php
namespace  Starscy\Project\models\Blog;

use Starscy\Project\models\User;

class Post 
{
    public function __construct(
        private int $id,
        private User $user,
        private string $title,
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


    public function getTitle():string
    {
        return $this->title;
    }

    public function setTitle(string $title):Post
    {
        $this->title=$title;
        return this;
    }

    public function getText():string
    {
        return $this->text;
    }

    public function setText(string $text):Post
    {
        $this->text=$text;
        return this;
    }

    public function __toString():string
    {
        return "$this->title \n $this->text .".PHP_EOL;
    }

}