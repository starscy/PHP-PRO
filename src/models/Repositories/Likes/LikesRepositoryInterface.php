<?php

namespace Starscy\Project\models\Repositories\Likes;

use Starscy\Project\models\Blog\Like;
use Starscy\Project\models\UUID;


interface LikesRepositoryInterface
{
    public function save(Like $like):void;
    public function getByPostUuid(UUID $uuid);
    public function checkIfPostBeenLikedByUser(UUID $postUuid, UUID $userUuid):void;
}