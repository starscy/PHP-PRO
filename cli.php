<?php
require_once __DIR__."/vendor/autoload.php";

use Starscy\Project\models\User;
use Starscy\Project\models\Repositories\Post\SqlitePostRepository;
use Starscy\Project\models\Repositories\User\SqliteUserRepository;
use Starscy\Project\models\Repositories\Comment\SqliteCommentRepository;
use Starscy\Project\models\Repositories\Post\PostRepository;
use Starscy\Project\models\Repositories\User\UserRepository;
use Starscy\Project\models\Repositories\Comment\CommentRepository;
use Starscy\Project\models\Commands\CreateUserCommand;
use Starscy\Project\models\Person\Name;
use Starscy\Project\models\UUID;
use Starscy\Project\models\Blog\Post;
use Starscy\Project\models\Blog\Comment;
use Starscy\Project\models\Commands\Arguments;
use Starscy\Project\models\Blog\Like;
use Starscy\Project\models\Repositories\Likes\SqliteLikesRepository;



// Подключаем файл bootstrap.php
// и получаем настроенный контейнер

$container = require __DIR__ . '/bootstrap.php';

// При помощи контейнера создаём команду

 $command = $container->get(CreateUserCommand::class);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
    echo "{$e->getMessage()}\n";
}


// $pdo = new PDO('sqlite:'.__DIR__.'/db.sqlite');

// $userRepository = new SqliteUserRepository($pdo);
// $postRepository = new SqlitePostRepository($pdo);
// $commentRepository = new SqliteCommentRepository($pdo);
// $likesRepository = new SqliteLikesRepository($pdo);

$faker = Faker\Factory::create('ru-Ru');

// $command = new CreateUserCommand($userRepository);

// try {
//     $command->handle(Arguments::fromArgv($argv));
// } catch (AppException $e) {
//     echo $e->getMessage();
// }



// $user = new User (
//     UUID::random(),
//     explode(" ", $faker->name())[0],
//     new Name (
//         explode(" ", $faker->name())[0], 
//         explode(" ", $faker->name())[1]
//     )
// );


// $user2 = new User (
//     UUID::random(),
//     explode(" ", $faker->name())[1],
//     new Name (
//         explode(" ", $faker->name())[0], 
//         explode(" ", $faker->name())[1]
//     )
// );
// $userRepository->save($user) ;


// $post = new Post (
//     UUID::random(),
//     $user,
//     $faker->text().PHP_EOL."!!!",
//     $faker->text()
// );
// $postRepository->save($post) ;

// $comment = new Comment (
//     UUID::random(),
//     $post,
//     $user,
//     $faker->text()
// );
// $commentRepository->save($comment) ;

// $like = new Like( 
//     UUID::random(),
//     $post,
//     $user
// );

// $like2 = new Like( 
//     UUID::random(),
//     $post,
//     $user2
// );

// $likesRepository->save($like);
// $likesRepository->save($like2);


// $likes= $likesRepository->getByPostUuid(new UUID('95ad53d2-6512-437d-92a0-f5a430d86c0c'));
// print_r($likes);

// $login=$userRepository->get('Диана');


// $userRepositoryInMem->save($user) ;

// $postRepositoryInMem->save($post) ;
// $commentRepositoryInMem->save($comment) ;



// try{
    //  $name=$userRepository->get(new UUID ('b695d14c-6a54-4f38-ad14-740c58537d56'));
    //  echo $name.PHP_EOL;
    // $login=$userRepository->getUserByLogin('Диана');
    // echo $login;
    // $testPost = $postRepository->getById('bb9ba065-cf7d-4455-b456-81c88f409ecf');
    // echo $testPost.PHP_EOL;
    //  $comTest = $commentRepository->get(new UUID('a2932b14-cbe0-4669-a39c-7936eeadc786'));
    //  var_damp($comTest);

// } catch  (Exception $e) {
//     echo $e->getMessage();
// }

// print_r($userRepository->getAllUsers());

// use Starscy\Project\models\Person\Person;
// use Starscy\Project\models\Repositories\UserRepository;
// use Starscy\Project\models\Blog\Post;
// use Starscy\Project\models\Blog\Comment;

// $userRepository =  new UserRepository();



// switch ($argv[1])
// {
//     case "user":
//         echo $user;
//         break;
//     case "post":
//         $post = new Post (
//             1,
//             $user,
//             $faker-> text(),
//             $faker-> text()
//         );
//         echo $post;
//         break;
//     case 'comment':
//         $comment = new Comment (
//             1,
//             $user,
//             $post = new Post (
//                 1,
//                 $user,
//                 $faker-> text(),
//                 $faker-> text()
//             ),
//             $faker-> text()
//         );
//         echo $comment;
//         break;
//     default:
//         break;
// }


// $name = new Name("vadim","karavaev");
// $person = new Person($name, new DateTimeImmutable());
// $user1 = new User(1,$name,'starscy');
// try {
//     $userRepository->saveUser($user1);
//     print_r($userRepository->getUser("1"));
//     print_r($userRepository->getUser("2"));
    
// } catch (UserNotFoundException | Exception $e) {
//    echo $e->getMessage();
// }

// $fakeuser = Faker\Factory::create('ru_Ru');

// print_r ($fakeuser->name());
