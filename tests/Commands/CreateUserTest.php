<?php

namespace Starscy\Project\UnitTests\Commands;

use Starscy\Project\models\Commands\Arguments;
use Starscy\Project\models\Exceptions\CommandException;
use Starscy\Project\models\Exceptions\ArgumentException;
use Starscy\Project\models\Exceptions\UserNotFoundException;
use Starscy\Project\models\Commands\User\CreateUser;
use Starscy\Project\models\Repositories\User\DummyUserRepository;
use Starscy\Project\models\Repositories\User\UserRepositoryInterface;
use Starscy\Project\models\User;
use Starscy\Project\models\UUID;
use PHPUnit\Framework\TestCase;
use Starscy\Project\UnitTests\DummyLogger;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @covers CreateUserCommandTest
 */


class CreateUserTest extends TestCase
{
    public function testItRequiresLastName(): void
    {
        $command = new CreateUser($this->makeUsersRepository());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "second_name")');

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'first_name' => 'Ivan',
                'password' => '123'
            ]),
            new NullOutput(),
        );
    }

    public function testItRequiresPassword(): void
    {
        $command = new CreateUser($this->makeUsersRepository());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "password")');

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'first_name' => 'Ivan',
                'second_name' => 'Nikitin',

            ]),
            new NullOutput(),
        );
    }

    public function testItRequiresFirstName(): void
    {
        $command = new CreateUser($this->makeUsersRepository());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "first_name")');

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => '123',
                'second_name' => 'Nikitin',
            ]),
            new NullOutput(),
        );
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

        $command = new CreateUser($userRepository);

        $command->run(
            new ArrayInput([
            'username' => 'Ivan',
            'first_name' => 'Ivan',
            'second_name' => 'Nikitin',
            'password' => '123'
            ]),
            new NullOutput(),
        );

        $this->assertTrue($userRepository->wasCalled());
    }

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
}
