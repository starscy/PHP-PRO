<?php

use Starscy\Project\models\Container\DIContainer;
use Starscy\Project\models\Repositories\Post\PostRepositoryInterface;
use Starscy\Project\models\Repositories\Post\SqlitePostRepository;
use Starscy\Project\models\Repositories\User\SqliteUserRepository;
use Starscy\Project\models\Repositories\User\UserRepositoryInterface;
use Starscy\Project\models\Repositories\Likes\LikesRepositoryInterface;
use Starscy\Project\models\Repositories\Likes\SqliteLikesRepository;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Starscy\Project\Http\Auth\IdentificationInterface;
use Starscy\Project\Http\Auth\JsonBodyUuidIdentification;

// Подключаем автозагрузчик Composer

require_once __DIR__ . '/vendor/autoload.php';

// Загружаем переменные окружения из файла .env

\Dotenv\Dotenv::createImmutable(__DIR__)->safeLoad();

// Создаём объект контейнера ..

$container = new DIContainer();

    // .. и настраиваем его:
    // 1. подключение к БД

$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . $_SERVER['SQLITE_DB_PATH'])
);

// 2. репозиторий статей

$container->bind(
    PostRepositoryInterface::class,
    SqlitePostRepository::class
);

// 3. репозиторий пользователей

$container->bind(
    UserRepositoryInterface::class,
    SqliteUserRepository::class
);

//
$container->bind(
    LikesRepositoryInterface::class,
    SqliteLikesRepository::class
);

$logger = (new Logger('blog'));

    // Включаем логирование в файлы,
    // если переменная окружения LOG_TO_FILES
    // содержит значение 'yes'

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

    // Включаем логирование в консоль,
    // если переменная окружения LOG_TO_CONSOLE
    // содержит значение 'yes'

if ('yes' === $_SERVER['LOG_TO_CONSOLE']) {
    $logger
    ->pushHandler(
    new StreamHandler("php://stdout")
    );
}

$container->bind(
    LoggerInterface::class,
    $logger
);

$container->bind(
    IdentificationInterface::class,
    JsonBodyUuidIdentification::class
);

// Возвращаем объект контейнера

return $container;