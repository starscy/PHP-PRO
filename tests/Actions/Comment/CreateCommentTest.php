<?php


namespace Actions;

use Starscy\Project\Http\Actions\Comment\CreateComment;
use Starscy\Project\Http\ErrorResponse;
use Starscy\Project\models\Blog\Comment;
use Starscy\Project\models\Exceptions\JsonException;
use Starscy\Project\Http\Actions\Post\CreatePost;
use Starscy\Project\Http\Request;
use Starscy\Project\Http\SuccessfulResponse;
use Starscy\Project\models\Person\Name;
use Starscy\Project\models\Repositories\Comment\CommentRepositoryInterface;
use Starscy\Project\models\Repositories\User\UserRepositoryInterface;
use Starscy\Project\models\User;
use Starscy\Project\models\Exceptions\UserNotFoundException;
use Starscy\Project\models\Blog\Post;
use Starscy\Project\models\Repositories\Post\PostRepositoryInterface;
use Starscy\Project\models\UUID;
use Starscy\Project\models\Exceptions\PostNotFoundException;
use PHPUnit\Framework\TestCase;
use Starscy\Project\UnitTests\DummyLogger;
use Starscy\Project\Http\Auth\JsonBodyUsernameIdentification;
use Starscy\Project\Http\Auth\JsonBodyUuidIdentification;
use Starscy\Project\Http\Auth\AuthException;

class CreateCommentTest extends TestCase
{
    private function commentsRepository(): CommentRepositoryInterface
    {
        return new class() implements CommentRepositoryInterface {
            private bool $called = false;

            public function __construct()
            {
            }

            public function save(Comment $comment): void
            {
                $this->called = true;
            }

            public function get(UUID $uuid): Comment
            {
                throw new PostNotFoundException('Not found');
            }

             public function getCalled(): bool
            {
                return $this->called;
            }
        };

    }
    private function postsRepository(): PostRepositoryInterface
    {
        return new class() implements PostRepositoryInterface {

            public function __construct()
            {
            }

            public function save(Post $post): void
            {
                $this->called = true;
            }

            public function get(UUID $uuid): Post
            {
                throw new PostNotFoundException('Not found');
            }

            public function getByTitle(string $title): Post
            {
                throw new PostNotFoundException('Not found');
            }

            public function delete(UUID $uuid): void
            {
            }
        };
    }

    private function usersRepository(array $users): UserRepositoryInterface
    {
        return new class($users) implements UserRepositoryInterface {
            public function __construct(
                private array $users
            )
            {
            }

            public function save(User $user): void
            {
            }

            public function get(UUID $uuid): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && (string)$uuid == $user->uuid()) {
                        return $user;
                    }
                }
                throw new UserNotFoundException('Cannot find user: ' . $uuid);
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException('Not found');
            }
        };
    }

    public function testItReturnsSuccessAnswer(): void
    {
        $commentRepositoryStub = $this->createStub(CommentRepositoryInterface::class);
        $postsRepositoryStub = $this->createStub(PostRepositoryInterface::class);
        $authenticationStub = $this->createStub(JsonBodyUsernameIdentification::class);

        $authenticationStub
            ->method('user')
            ->willReturn(
                new User(
                    new UUID("8b1e3d16-023d-41d5-b9e6-87c3b89278d0"),
                    'username',
                    'password',
                    new Name('first', 'last'),

                )
            );

        $createComment = new CreateComment(
            $commentRepositoryStub,
            $postsRepositoryStub,
            $authenticationStub,
        );

        $request = new Request(
            [],
            [],
            '{
                   "post_uuid":"8b1e3d16-023d-41d5-b9e6-87c3b89278d9,
                 "text": "lorem"
                }'
        );

        $actual = $createComment($request);

        $this->assertInstanceOf(
            SuccessFulResponse::class,
            $actual
        );
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws \JsonException
     */
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request([], [], '{"author_uuid":"8b1e3d16-023d-41d5-b9e6-87c3b89278d0","title":"title","text":"text"}');

        $authenticationStub = $this->createStub(JsonBodyUuidIdentification::class);
        $authenticationStub
            ->method('user')
            ->willReturn(
                new User(
                    new UUID("8b1e3d16-023d-41d5-b9e6-87c3b89278d0"),
                    'username',
                    'password',
                    new Name('first', 'last'),

                )
            );

        $postsRepository = $this->postsRepository();

        $action = new CreatePost($postsRepository, $authenticationStub, new DummyLogger());

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);

        $this->setOutputCallback(function ($data) {
            $dataDecode = json_decode(
                $data,
                associative: true,
                flags: JSON_THROW_ON_ERROR
            );

            $dataDecode['data']['uuid'] = "351739ab-fc33-49ae-a62d-b606b7038c87";
            return json_encode(
                $dataDecode,
                JSON_THROW_ON_ERROR
            );
        });

        $this->expectOutputString('{"success":true,"data":{"uuid":"351739ab-fc33-49ae-a62d-b606b7038c87"}}');


        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorResponseIfNotFoundUser(): void
    {
        $request = new Request([], [], '{"author_uuid":"8b1e3d16-023d-41d5-b9e6-87c3b89278d0","title":"title","text":"text"}');

        $postsRepositoryStub = $this->createStub(PostRepositoryInterface::class);
        $authenticationStub = $this->createStub(JsonBodyUuidIdentification::class);

        $authenticationStub
            ->method('user')
            ->willThrowException(
                new AuthException('Cannot find user: 8b1e3d16-023d-41d5-b9e6-87c3b89278d0')
            );

        $action = new CreatePost($postsRepositoryStub, $authenticationStub, new DummyLogger());

        $response = $action->handle($request);

        $response->send();

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"Cannot find user: 8b1e3d16-023d-41d5-b9e6-87c3b89278d0"}');

    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws JsonException
     */
    public function testItReturnsErrorResponseIfNoTextProvided(): void
    {
        $request = new Request([], [], '{"author_uuid":"10373537-0805-4d7a-830e-22b481b4859c","title":"title"}');

        $postsRepository = $this->postsRepository([]);
        $authenticationStub = $this->createStub(JsonBodyUuidIdentification::class);
        $authenticationStub
            ->method('user')
            ->willReturn(
                new User(
                    new UUID("10373537-0805-4d7a-830e-22b481b4859c"),
                    'username',
                    'password',
                    new Name('first', 'last'),

                )
            );

        $action = new CreatePost($postsRepository, $authenticationStub, new DummyLogger());

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"No such field: text"}');

        $response->send();
    }
}