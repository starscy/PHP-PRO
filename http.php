<?php

use Starscy\Project\Http\Actions\IndexAction;
use Starscy\Project\Http\Request;
use Starscy\Project\Http\SuccessfulResponse;
use Starscy\Project\models\Exceptions\HttpException;
use Starscy\Project\Http\Actions\User\FindByUsername;
use Starscy\Project\Http\Actions\Post\CreatePost;
use Starscy\Project\Http\Actions\Comment\CreateComment;
use Starscy\Project\Http\Actions\User\CreateUser;
use Starscy\Project\Http\Actions\Post\DeletePost;
use Starscy\Project\models\Repositories\User\SqliteUserRepository;
use Starscy\Project\models\Repositories\Post\SqlitePostRepository;
use Starscy\Project\models\Repositories\Comment\SqliteCommentRepository;
use Starscy\Project\Http\ErrorResponse;


require_once __DIR__ . '/vendor/autoload.php';


// Создаём объект запроса из суперглобальных переменных

$request = new Request(
    $_GET, 
    $_SERVER, 
    file_get_contents('php://input')
);

$routes = [

    'GET' => [
        '/' => new IndexAction(),
        '/users/show' => new FindByUsername(
            new SqliteUserRepository(
                new PDO('sqlite:' . __DIR__ . '/db.sqlite')
            )
        )
        // Второй маршрут

        // '/posts/show' => new FindByUuid(
        // new SqlitePostRepository(
        // new PDO('sqlite:' . __DIR__ . '/db.sqlite')
        // )
        // ),
    ],


    'POST' => [
        '/users/create' => new CreateUser(
            new SqliteUserRepository(
                new PDO('sqlite:' . __DIR__ . '/db.sqlite')
            )
        ),

        '/posts/create' => new CreatePost(
            new SqlitePostRepository(
                new PDO('sqlite:' . __DIR__ . '/db.sqlite')
            ),
            new SqliteUserRepository(
                new PDO('sqlite:' . __DIR__ . '/db.sqlite')
            )
        ),

        '/posts/comment' => new CreateComment(
            new SqliteCommentRepository(
                new PDO('sqlite:' . __DIR__ . '/db.sqlite')
            ),
            new SqlitePostRepository(
                new PDO('sqlite:' . __DIR__ . '/db.sqlite')
            ),
            new SqliteUserRepository(
                new PDO('sqlite:' . __DIR__ . '/db.sqlite')
            ),
        ),
    ],

    'DELETE' =>[

        '/posts' => new DeletePost(
            new SqlitePostRepository(
                new PDO('sqlite:' . __DIR__ . '/db.sqlite')
            ),
        ),
    ],
    

];

// Получаем данные из объекта запроса

try{
    $path = $request->path();
} catch (HttpException){
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

    // Если у нас нет маршрутов  для этого метода
    // отправляем неуспешный ответ

if (!array_key_exists($method, $routes)) {
    (new ErrorResponse('Not found this route with that method'))->send();
    return;
}

// Ищем маршрут среди маршрутов для этого метода
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Not found'))->send();
    return;
}


    // Выбираем найденное действие

$action = $routes[$method][$path];

try {
        // Пытаемся выполнить действие,
        // при этом результатом может быть
        // как успешный, так и неуспешный ответ

        $response = $action->handle($request);
        $response->send();

} catch (Exception $e) {

        // Отправляем неудачный ответ
        // если что-то пошло не так

    (new ErrorResponse($e->getMessage()))->send();
}
 

