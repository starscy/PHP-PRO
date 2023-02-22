<?php

namespace Starscy\Project\models\Commands;

use Starscy\Project\models\Person\Name;
use Starscy\Project\models\Exceptions\UserNotFoundException;
use Starscy\Project\models\Exceptions\CommandException;
use Starscy\Project\models\Repositories\User\UserRepositoryInterface;
use Starscy\Project\models\User;
use Starscy\Project\models\UUID;
use Psr\Log\LoggerInterface;


class CreateUserCommand
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private LoggerInterface $logger,
    ) 
    {
    }

    public function handle(Arguments $arguments): void
    {
        // Логируем информацию о том, что команда запущена
        // Уровень логирования – INFO

        $this->logger->info("Create user command started");

        $username = $arguments->get('username');

        if ($this->userExists($username)) {
            $this->logger->warning("User already exists: $username");
            throw new CommandException("User already exists: $username");
        }

        $user = User::createForm(
            $username,
            $arguments->get('password'),
            new Name(
                $arguments->get('first_name'),
                $arguments->get('second_name'))
        );

        $this->userRepository->save($user);

        // Логируем информацию о новом пользователе
        
        $this->logger->info("User created: ".$user->uuid());
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