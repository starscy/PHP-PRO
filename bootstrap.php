<?php

use Starscy\Project\models\Container\DIContainer;
use Starscy\Project\models\Repositories\Post\PostRepositoryInterface;
use Starscy\Project\models\Repositories\Post\SqlitePostRepository;
use Starscy\Project\models\Repositories\User\SqliteUserRepository;
use Starscy\Project\models\Repositories\User\UserRepositoryInterface;
use Starscy\Project\models\Repositories\Likes\LikesRepositoryInterface;
use Starscy\Project\models\Repositories\Likes\SqliteLikesRepository;

// Подключаем автозагрузчик Composer

require_once __DIR__ . '/vendor/autoload.php';

// Создаём объект контейнера ..

$container = new DIContainer();

    // .. и настраиваем его:
    // 1. подключение к БД

    $container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/db.sqlite')
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

// Возвращаем объект контейнера

return $container;