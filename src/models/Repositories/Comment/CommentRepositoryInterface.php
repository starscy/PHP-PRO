<?php

namespace Starscy\Project\models\Repositories\Comment;

use Starscy\Project\models\Blog\Comment;
use Starscy\Project\models\UUID;

interface CommentRepositoryInterface
{
    public function save(Comment $comment):void;
    public function get(UUID $uuid): Comment;
}