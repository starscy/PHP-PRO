<?php

namespace Starscy\Project\models\Commands;

use Starscy\Project\models\Person\Name;
use Starscy\Project\models\Exceptions\UserNotFoundException;
use Starscy\Project\models\Exceptions\CommandException;
use Starscy\Project\models\Repositories\User\UserRepositoryInterface;
use Starscy\Project\models\User;
use Starscy\Project\models\UUID;


class CreateUserCommand
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) 
    {
    }

    public function handle(Arguments $arguments): void
    {
        $username = $arguments->get('username');

        if ($this->userExists($username)) {
            throw new CommandException("User already exists: $username");
        }
    
        $this->userRepository->save(new User(
            UUID::random(),
            $username,
            new Name( $arguments->get('first_name'),  $arguments->get('last_name'))
        ));
    }
    
    private function userExists(string $username): bool
    {
        try {
        $this->userRepository->getByUsername($username);
        } catch (UserNotFoundException) {
        return false;
        }
        return true;
    }
}