<?php

namespace Actions\User;

use Starscy\Project\Http\ErrorResponse;
use Starscy\Project\models\Commands\Arguments;
use Starscy\Project\models\Commands\CreateUserCommand;
use Starscy\Project\models\Exceptions\ArgumentException;
use Starscy\Project\models\Exceptions\JsonException;
use Starscy\Project\Http\Actions\User\CreateUser;
use Starscy\Project\Http\Request;
use Starscy\Project\Http\SuccessfulResponse;
use Starscy\Project\models\Person\Name;
use Starscy\Project\models\Repositories\User\UserRepositoryInterface;
use Starscy\Project\models\User;
use Starscy\Project\models\Exceptions\UserNotFoundException;
use Starscy\Project\models\UUID;
use PHPUnit\Framework\TestCase;
use Starscy\Project\UnitTests\DummyLogger;

class CreateUserTest extends TestCase
{
    private function usersRepository(): UserRepositoryInterface
    {
        return new class() implements UserRepositoryInterface {
            private bool $called = false;

            public function __construct()
            {
            }

            public function save(User $user): void
            {
                $this->called = true;
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException('Not found');
            }

            public function getByUsername(string $title): User
            {
                throw new UserNotFoundException('Not found');
            }

            public function getCalled(): bool
            {
                return $this->called;
            }
        };
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request([], [], '{"username":"TestUser","password":"123", "first_name":"Test","second_name":"Robot"}');

        $usersRepository = $this->usersRepository();

        $action = new CreateUser($usersRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);

        $this->setOutputCallback(function ($data){
            $dataDecode = json_decode(
                $data,
                associative: true,
                flags: JSON_THROW_ON_ERROR
            );

            $dataDecode['data']['uuid'] = "151739ab-fc33-49ae-a62d-b606b7038c87";
            return json_encode(
                $dataDecode,
                JSON_THROW_ON_ERROR
            );
        });

        $this->expectOutputString('{"success":true,"data":{"uuid":"151739ab-fc33-49ae-a62d-b606b7038c87"}}');


        $response->send();
    }

    public function testItRequiresPassword(): void
    {
        $command = new CreateUserCommand(
            $this->usersRepository(),
            new DummyLogger()
        );
        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage('No such argument: password');
        $command->handle(new Arguments([
            'username' => 'Ivan',
        ]));
    }


}