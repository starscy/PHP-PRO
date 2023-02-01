<?php
namespace  Starscy\Project\models\Blog;

use Starscy\Project\models\User;
use  Starscy\Project\models\UUID;

class Post 
{
    public function __construct(
        private UUID $uuid,
        private User $user,
        private string $title,
        private string $text
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