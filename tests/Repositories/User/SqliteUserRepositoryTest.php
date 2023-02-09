<?php

namespace Starscy\Project\UnitTests\Repositories\User;

use Starscy\Project\models\Repositories\User\SqliteUserRepository;
use Starscy\Project\models\Exceptions\UserNotFoundException;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Starscy\Project\models\User;
use Starscy\Project\models\UUID;
use  Starscy\Project\models\Person\Name;

class SqliteUserRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenUserNotFound(): void
    {
        $connectionStub = $this->createStub(PDO::class);

        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);
        
        $connectionStub->method('prepare')->willReturn(
            $statementStub       
        );
        $repository = new SqliteUserRepository($connectionStub);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('Cannot find user: Ivan');
 
        $repository->getByUsername('Ivan');

    }

    public function testItSavesUserToDatabase(): void
    {
        // 2. Создаём стаб подключения
        $connectionStub = $this->createStub(PDO::class);

        // 4. Создаём мок запроса, возвращаемый стабом подключения
        $statementMock = $this->createMock(PDOStatement::class);

        // 5. Описываем ожидаемое взаимодействие
        // нашего репозитория с моком запроса
        $statementMock
            ->expects($this->once()) // Ожидаем, что будет вызван один раз
            ->method('execute') // метод execute
            ->with([
                    // с единственным аргументом - массивом
                    ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                    ':username' => 'ivan123',
                    ':first_name' => 'Ivan',
                    ':second_name' => 'Nikitin',
        ]);

        // 3. При вызове метода prepare стаб подключения
        // возвращает мок запроса
        $connectionStub->method('prepare')->willReturn($statementMock);

        // 1. Передаём в репозиторий стаб подключения
        $repository = new SqliteUserRepository($connectionStub);
        
        // Вызываем метод сохранения пользователя
        $repository->save(
            new User( // Свойства пользователя точно такие,
            // как и в описании мока
            new UUID('123e4567-e89b-12d3-a456-426614174000'),
            'ivan123',
            new Name('Ivan', 'Nikitin')
            )
        );
    }

    public function testItGetUserByUuid(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            'uuid' => '6ca3e4a4-11f3-4dfc-972a-960c9034af8f',
            'username' => '	Пётр',
            'first_name' => 'Быков',
            'second_name' => 'Крюков',
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $UserRepository = new SqliteUserRepository($connectionStub);
        $User = $UserRepository->get(new UUID('6ca3e4a4-11f3-4dfc-972a-960c9034af8f'));

        $this->assertSame('6ca3e4a4-11f3-4dfc-972a-960c9034af8f', (string)$User->uuid());
    }
}
