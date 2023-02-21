<?php

namespace Starscy\Project\models\Repositories\User;

use Starscy\Project\models\User;
use Starscy\Project\models\Person\Name;
use Starscy\Project\models\UUID;
use PDO;
use Starscy\Project\models\Exceptions\UserNotFoundException;

class SqliteUserRepository implements UserRepositoryInterface
{
    public function __construct (
       private PDO $pdo,

    )
    {
    }

    public function save(User $user):void
    {
        $statement = $this->pdo->prepare(
        'INSERT INTO users (uuid, username, password, first_name, second_name)
        VALUES (:uuid,:username,:password, :first_name, :second_name)
        ON CONFLICT (uuid) DO UPDATE SET
            first_name = :first_name,
            second_name = :second_name',
        );  

        $statement->execute([
            ':uuid' => (string)$user->uuid(),
            ':username'=> (string)$user->getUsername(),
            ':password'=>$user->hashedPassword(),
            ':first_name' => $user->getName()->getFirst(),
            ':second_name' => $user->getName()->getSecond(),
        ]);

    }

    public function get(UUID $uuid):User
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM users WHERE uuid = :uuid'
        );   

        $statement->execute([
            ':uuid' => (string)$uuid
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);
        
        if ($result === false){
            throw new UserNotFoundException("user with id $uuid not found");
        }
        
        return new User(
            new UUID($result['uuid']),
            $result['username'],
            $result['password'],
            new Name($result['first_name'],
            $result['second_name'])
        );
    }

    public function getByUsername (string $username):User
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM users WHERE username = :username'
        );

        $statement->execute([
            ':username' => $username
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

              
        if ($result === false){
            throw new UserNotFoundException("Cannot find user: $username ");
        }
        
        return new User(
            new UUID($result['uuid']),
            $result['username'],
            $result['password'],
            new Name($result['first_name'], $result['second_name'])
            );

    }

}
