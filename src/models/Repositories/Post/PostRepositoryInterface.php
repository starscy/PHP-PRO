<?php

namespace Starscy\Project\models\Repositories\Post;

use Starscy\Project\models\Blog\Post;
use Starscy\Project\models\UUID;


interface PostRepositoryInterface
{
    public function save(Post $post):void;
    public function get(UUID $uuid): Post;
}