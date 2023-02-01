<?php

namespace Starscy\Project\UnitTests\Commands;

use Starscy\Project\models\Commands\Arguments;
use Starscy\Project\models\Exceptions\CommandException;
use Starscy\Project\models\Exceptions\ArgumentException;
use Starscy\Project\models\Exceptions\UserNotFoundException;
use Starscy\Project\models\Commands\CreateUserCommand;
use Starscy\Project\models\Repositories\User\DummyUserRepository;
use Starscy\Project\models\Repositories\User\UserRepositoryInterface;
use Starscy\Project\models\User;
use Starscy\Project\models\UUID;
use PHPUnit\Framework\TestCase;

/**
 * @covers CreateUserCommandTest
 */


class CreateUserCommandTest extends TestCase
{
    private function makeUsersRepository(): UserRepositoryInterface
    {
        return new class implements UserRepositoryInterface {

            public function save( $user): void
            {
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }
        };
    }

    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {

        $command = new CreateUserCommand(
            new DummyUserRepository()
            // new CreateUserCommand($this->makeUsersRepository())
        );

        $this->expectException(CommandException::class);
    
        $this->expectExceptionMessage('User already exists: Ivan'); 

        $command->handle(new Arguments(['username' => 'Ivan']));
    }

    public function testItRequiresFirstName(): void
    {
        $command = new CreateUserCommand($this->makeUsersRepository());

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage('No such argument: first_name');

        $command->handle(new Arguments(['username' => 'Ivan']));
    }
        
    public function testItRequiresLastName(): void
    {
        $command = new CreateUserCommand($this->makeUsersRepository());
    
        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage('No such argument: last_name');

        $command->handle(new Arguments([
                'username' => 'Ivan',
                'first_name' => 'Ivan',
        ]));
    }

    public function testItSavesUserToRepository(): void
    { 
        $userRepository = new class implements UserRepositoryInterface {

            private bool $called = false;

            public function save(User $user): void
            {
                $this->called = true;
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }
            
            public function wasCalled(): bool
            {
                return $this->called;
            }
        };
     
        $command = new CreateUserCommand($userRepository);
       
        $command->handle(new Arguments([
            'username' => 'Ivan',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
        ]));

        $this->assertTrue($userRepository->wasCalled());
    }
    
}
            