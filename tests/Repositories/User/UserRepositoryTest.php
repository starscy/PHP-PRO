<?php

namespace Starscy\Project\models\Repositories\User;

use Starscy\Project\models\User;
use Starscy\Project\models\Exceptions\UserNotFoundException;
use Starscy\Project\models\UUID;
use PHPUnit\Framework\TestCase;
use PDO;
use PDOStatement;


class UserRepositoryTest  extends TestCase
{
    // public function testItThrowsAnExceptionWhenUserNotFound(): void
    // {
    //         $connectionStub = $this->createStub(PDO::class);
    
    //         $statementStub = $this->createStub(PDOStatement::class);
    
    //         $statementStub->method('fetch')->willReturn(false);
            
    //         $connectionStub->method('prepare')->willReturn(
    //             $statementStub       
    //         );
    //         $repository = new UserRepository($connectionStub);
    
    //         $this->expectException(UserNotFoundException::class);
    //         $this->expectExceptionMessage('Cannot find user: Ivan');
     
    //         $repository->getByUsername('Ivan');
    
    // }
}