<?php

namespace Starscy\Project\models\Repositories\User;

use Starscy\Project\models\User;
use Starscy\Project\models\UUID;

interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function get(UUID $uuid): User;
    public function getByUsername(string $username):User;
}