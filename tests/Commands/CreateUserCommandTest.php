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
use Starscy\Project\UnitTests\DummyLogger;

/**
 * @covers CreateUserCommandTest
 */


class CreateMyUserCommandTest extends TestCase
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
            new DummyUserRepository(),
            // $this->makeUsersRepository(),
            new DummyLogger(),
            // new CreateUserCommand($this->makeUsersRepository())
        );

        $this->expectException(CommandException::class);
    
        $this->expectExceptionMessage('User already exists: Ivan'); 

        $command->handle(new Arguments([
            'username' => 'Ivan',
            'password' => '123'
        ]));
    }

    public function testItRequiresFirstName(): void
    {
        $command = new CreateUserCommand($this->makeUsersRepository(),new DummyLogger());

        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage('No such argument: first_name');

        $command->handle(new Arguments([
            'username' => 'Ivan',
            'password' => '123'
        ]));
    }
        
    public function testItRequiresLastName(): void
    {
        $command = new CreateUserCommand($this->makeUsersRepository(),new DummyLogger());
    
        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage('No such argument: second_name');

        $command->handle(new Arguments([
                'username' => 'Ivan',
                'first_name' => 'Ivan',
                'password' => '123'
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
     
        $command = new CreateUserCommand($userRepository,new DummyLogger());
       
        $command->handle(new Arguments([
            'username' => 'Ivan',
            'first_name' => 'Ivan',
            'second_name' => 'Nikitin',
            'password' => '123'
        ]));

        $this->assertTrue($userRepository->wasCalled());
    }
    
}
            