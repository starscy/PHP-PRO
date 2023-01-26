<?php

require_once __DIR__."/vendor/autoload.php";

use Starscy\Project\models\Person\Name;
use Starscy\Project\models\User;
use Starscy\Project\models\Person\Person;
use Starscy\Project\models\Repositories\UserRepository;
use Starscy\Project\models\Blog\Post;
use Starscy\Project\models\Blog\Comment;

$userRepository =  new UserRepository();
$faker = Faker\Factory::create('ru_RU');

$user = new User (
    1,
    new Name (
        explode(" ", $faker->name())[0], 
        explode(" ", $faker->name())[2]
    )
);

switch ($argv[1])
{
    case "user":
        echo $user;
        break;
    case "post":
        $post = new Post (
            1,
            $user,
            $faker-> text(),
            $faker-> text()
        );
        echo $post;
        break;
    case 'comment':
        $comment = new Comment (
            1,
            $user,
            $post = new Post (
                1,
                $user,
                $faker-> text(),
                $faker-> text()
            ),
            $faker-> text()
        );
        echo $comment;
        break;
    default:
        break;
}


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
