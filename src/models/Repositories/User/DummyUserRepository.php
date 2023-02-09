<?php
namespace Starscy\Project\models\Repositories\User;

use Starscy\Project\models\User;
use Starscy\Project\models\UUID;
use Starscy\Project\models\Exceptions\UserNotFoundException;
use  Starscy\Project\models\Person\Name;

class DummyUserRepository implements UserRepositoryInterface
{
    public function save(User $user): void
    {
        //TODO
    }
    public function get(UUID $uuid): User
    {
        throw new UserNotFoundException('user not found');
    }
    public function getByUsername(string $username):User
    {
        return new User(UUID::random(), "username", new Name("first","second"));
    }
}