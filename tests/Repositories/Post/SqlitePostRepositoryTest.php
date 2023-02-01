<?php

namespace Starscy\Project\UnitTests\Repositories\Post;

use Starscy\Project\models\Repositories\Post\SqlitePostRepository;
use Starscy\Project\models\Exceptions\PostNotFoundException;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Starscy\Project\models\Blog\Post;
use Starscy\Project\models\UUID;
use  Starscy\Project\models\Person\Name;
use  Starscy\Project\models\User;

/**
 *@covers SqlitePostRepositoryTest
 */

class SqlitePostRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenPostNotFound(): void
    {
        $connectionStub = $this->createStub(PDO::class);

        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);
        
        $connectionStub->method('prepare')->willReturn(
            $statementStub       
        );
        $repository = new SqlitePostRepository($connectionStub);

        $this->expectException(PostNotFoundException::class);
        $this->expectExceptionMessage('Cannot find post: 123e4567-e89b-12d3-a499-426614174999');
        
        $repository->get(new UUID('123e4567-e89b-12d3-a499-426614174999'));

    }

    public function testItSavesPostToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);

        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once()) 
            ->method('execute') 
            ->with([
                    ':uuid' => '123e4567-e89b-12d3-a499-426614174000',
                    ':author_uuid' => '123e4567-e89b-12d3-a499-426614174033',
                    ':title' => 'Title',
                    ':text' => "Text test test test",
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqlitePostRepository($connectionStub);

        
        $user = new User( 
                new UUID('123e4567-e89b-12d3-a499-426614174033'),
                'ivan123',
                new Name('ivan', 'Nikitin')
        );

        $repository->save(    
            new Post(
                new UUID('123e4567-e89b-12d3-a499-426614174000'),
                $user,
                "Title",
                "Text test test test"
            )
        );
    }

    public function testItGetPostByUuid(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            'uuid' => '7b094211-1881-40f4-ac73-365ad0b2b2d4',
            'author_uuid' => '5a91ed7a-0ae4-495f-b666-c52bc8f13fe4',
            'title' => 'Заголовок',
            'text' => 'Какой-то текст',
             'username' => 'ivan123',
             'first_name' => 'Ivan',
             'second_name' => 'Nikitin',
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $postRepository = new SqlitePostRepository($connectionStub);
        $post = $postRepository->get(new UUID('7b094211-1881-40f4-ac73-365ad0b2b2d4'));

        $this->assertSame('7b094211-1881-40f4-ac73-365ad0b2b2d4', (string)$post->uuid());
    }

   

}
