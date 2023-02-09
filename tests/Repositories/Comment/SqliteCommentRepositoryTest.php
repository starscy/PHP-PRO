<?php

namespace Starscy\Project\UnitTests\Repositories\Comment;

use Starscy\Project\models\Repositories\Comment\SqliteCommentRepository;
use Starscy\Project\models\Exceptions\CommentNotFoundException;
use PDO;
use PDOStatement;
use Starscy\Project\models\Blog\Comment;
use Starscy\Project\models\Blog\Post;
use Starscy\Project\models\UUID;
use  Starscy\Project\models\Person\Name;
use  Starscy\Project\models\User;
use PHPUnit\Framework\TestCase;

/**
 * @covers SqliteCommentRepositoryTest
 */

class SqliteCommentRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenCommentNotFound(): void
    {
        $connectionStub = $this->createStub(PDO::class);

        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);
        
        $connectionStub->method('prepare')->willReturn(
            $statementStub       
        );
        $repository = new SqliteCommentRepository($connectionStub);

        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage('Cannot find Comment: 123e4567-e89b-12d3-a499-426614174999');
        
        $repository->get(new UUID('123e4567-e89b-12d3-a499-426614174999'));

    }

    public function testItSavesUserToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);

        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once()) 
            ->method('execute') 
            ->with([
                    ':uuid' => '000e4567-e89b-12d3-a499-426614174033',
                    ':post_uuid' => '123e4567-e89b-12d3-a499-426614174000',
                    ':author_uuid' => '123e4567-e89b-12d3-a499-426614174033',
                    ':text' => "Comment test",
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqliteCommentRepository($connectionStub);

        
        $user = new User( 
                new UUID('123e4567-e89b-12d3-a499-426614174033'),
                'ivan123',
                new Name('ivan', 'Nikitin')
        );

        $post =  new Post(
                new UUID('123e4567-e89b-12d3-a499-426614174000'),
                $user,
                "Title",
                "Text test test test"
        );

        $repository->save(    
            new Comment(
                new UUID('000e4567-e89b-12d3-a499-426614174033'),
                $post,
                $user,
                "Comment test"        
            )
        );
    }

    public function testItGetCommentByUuid(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            'uuid' => 'a2932b14-cbe0-4669-a39c-7936eeadc786',
            'post_uuid' => 'e7efba0a-bef9-42e7-8873-8d916c275a0b',
            'author_uuid' => '6ca3e4a4-11f3-4dfc-972a-960c9034af8f',
            'text' => 'Заголовок',
            // 'text' => 'Какой-то текст',
            // 'username' => 'ivan123',
            // 'first_name' => 'Ivan',
            // 'second_name' => 'Nikitin',
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $commentRepository = new SqliteCommentRepository($connectionStub);
        $comment = $commentRepository->get(new UUID        ('a2932b14-cbe0-4669-a39c-7936eeadc786'));

        $this->assertSame('a2932b14-cbe0-4669-a39c-7936eeadc786', (string)$comment->uuid());
    }
}
