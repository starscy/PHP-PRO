<?php

use Dotenv\Dotenv;
use Faker\Provider\ru_RU\Internet;
use Faker\Provider\Lorem;
use Faker\Provider\ru_RU\Person;
use Faker\Provider\ru_RU\Text;
use Starscy\Project\Http\Auth\AuthenticationInterface;
use Starscy\Project\Http\Auth\BearerTokenAuthentication;
use Starscy\Project\Http\Auth\JsonBodyUsernameIdentification;
use Starscy\Project\Http\Auth\PasswordAuthentication;
use Starscy\Project\Http\Auth\PasswordAuthenticationInterface;
use Starscy\Project\Http\Auth\TokenAuthenticationInterface;
use Starscy\Project\models\Container\DIContainer;
use Starscy\Project\models\Repositories\Comment\CommentRepositoryInterface;
use Starscy\Project\models\Repositories\Comment\SqliteCommentRepository;
use Starscy\Project\models\Repositories\Post\PostRepositoryInterface;
use Starscy\Project\models\Repositories\Post\SqlitePostRepository;
use Starscy\Project\models\Repositories\Token\AuthTokensRepositoryInterface;
use Starscy\Project\models\Repositories\Token\SqliteAuthTokensRepository;
use Starscy\Project\models\Repositories\User\SqliteUserRepository;
use Starscy\Project\models\Repositories\User\UserRepositoryInterface;
use Starscy\Project\models\Repositories\Likes\LikesRepositoryInterface;
use Starscy\Project\models\Repositories\Likes\SqliteLikesRepository;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Starscy\Project\Http\Auth\JsonBodyUuidIdentification;

require_once __DIR__ . '/vendor/autoload.php';

Dotenv::createImmutable(__DIR__)->safeLoad();

$container = new DIContainer();

$faker= new \Faker\Generator();

$faker->addProvider(new Person($faker));
$faker->addProvider(new Text($faker));
$faker->addProvider(new Internet($faker));
$faker->addProvider(new Lorem($faker));

$container->bind(
    \Faker\Generator::class,
    $faker
);

$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . $_SERVER['SQLITE_DB_PATH'])
);

$container->bind(
    PostRepositoryInterface::class,
    SqlitePostRepository::class
);

$container->bind(
    UserRepositoryInterface::class,
    SqliteUserRepository::class
);

$container->bind(
    CommentRepositoryInterface::class,
    SqliteCommentRepository::class
);

$container->bind(
    LikesRepositoryInterface::class,
    SqliteLikesRepository::class
);

//logger

$logger = (new Logger('blog'));

if ('yes' === $_SERVER['LOG_TO_FILES']) {
    $logger
    ->pushHandler(new StreamHandler(
        __DIR__ . '/logs/blog.log'
        ))
    ->pushHandler(new StreamHandler(
        __DIR__ . '/logs/blog.error.log',
        level: Logger::ERROR,
        bubble: false,
        ));
}

if ('yes' === $_SERVER['LOG_TO_CONSOLE']) {
    $logger
    ->pushHandler(
    new StreamHandler("php://stdout")
    );
}

/////

$container->bind(
    LoggerInterface::class,
    $logger
);

$container->bind(
    AuthenticationInterface::class,
    JsonBodyUuidIdentification::class
);

$container->bind(
    AuthenticationInterface::class,
    JsonBodyUsernameIdentification::class
);

$container->bind(
    AuthenticationInterface::class,
    PasswordAuthentication::class
);

$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);
$container->bind(
    AuthTokensRepositoryInterface::class,
    SqliteAuthTokensRepository::class
);

$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);
$container->bind(
    TokenAuthenticationInterface::class,
    BearerTokenAuthentication::class
);


// Возвращаем объект контейнера

return $container;