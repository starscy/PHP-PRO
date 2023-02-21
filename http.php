<?php

use Starscy\Project\Http\Actions\Auth\LogIn;
use Starscy\Project\Http\Actions\Auth\LogOut;
use Starscy\Project\Http\Request;
use Starscy\Project\Http\SuccessfulResponse;
use Starscy\Project\models\Exceptions\HttpException;
use Starscy\Project\Http\Actions\User\FindByUsername;
use Starscy\Project\Http\Actions\Post\CreatePost;
use Starscy\Project\Http\Actions\Comment\CreateComment;
use Starscy\Project\Http\Actions\User\CreateUser;
use Starscy\Project\Http\Actions\Likes\CreatePostLike;
use Starscy\Project\Http\Actions\Post\DeletePost;
use Starscy\Project\Http\ErrorResponse;
use Psr\Log\LoggerInterface;

// Подключаем файл bootstrap.php
// и получаем настроенный контейнер

$container = require __DIR__ . '/bootstrap.php';

$logger = $container->get(LoggerInterface::class);

// Создаём объект запроса из суперглобальных переменных

$request = new Request(
    $_GET, 
    $_SERVER, 
    file_get_contents('php://input')
);


// Получаем данные из объекта запроса

try{
    $path = $request->path();
} catch (HttpException $e){
    $logger->warning($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException $e) {
    $logger->warning($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse)->send();
    return;
}

// Ассоциируем маршруты с именами классов действий,
// вместо готовых объектов

$routes = [

    'GET' => [
        '/users/show' => FindByUsername::class,
//        '/posts/show' => FindByUuid::class,

        // Второй маршрут(вариант без DIContaner)

        // '/posts/show' => new FindByUuid(
        // new SqlitePostRepository(
        // new PDO('sqlite:' . __DIR__ . '/db.sqlite')
        // )
        // ),
    ],

    'POST' => [
        '/login' => LogIn::class,
        '/logout' => LogOut::class,
        '/users/create' => CreateUser::class,
        '/posts/create' => CreatePost::class,
        '/posts/comment' => CreateComment::class,
        '/posts/fav' => CreatePostLike::class,

    ],

    'DELETE' =>[
        '/posts' => DeletePost::class,
    ],
];

    // Если у нас нет маршрутов  для этого метода
    // отправляем неуспешный ответ

if (!array_key_exists($method, $routes)) {
    $message = "Route not found: $method $path";
    $logger->notice($message);
    (new ErrorResponse($message))->send();
    return;
}

// Ищем маршрут среди маршрутов для этого метода
if (!array_key_exists($path, $routes[$method])) {
    $message = "Route not found: $method $path";
    $logger->notice($message);
    (new ErrorResponse($message))->send();
    return;
}

// Получаем имя класса действия для маршрута

$actionClassName = $routes[$method][$path];

// С помощью контейнера
// создаём объект нужного действия

$action = $container->get($actionClassName);

try {

        // Пытаемся выполнить действие,
        // при этом результатом может быть
        // как успешный, так и неуспешный ответ

    $response = $action->handle($request);
    $response->send();
} catch (Exception $e) {

        // Отправляем неудачный ответ
        // если что-то пошло не так

    $logger->error($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse($e->getMessage()))->send();
}



