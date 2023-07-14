<?php

namespace Starscy\Project\models\Commands\User;

use Starscy\Project\models\Exceptions\UserNotFoundException;
use Starscy\Project\models\Person\Name;
use Starscy\Project\models\Repositories\User\UserRepositoryInterface;
use Starscy\Project\models\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUser extends Command
{
    public function __construct(
        private UserRepositoryInterface $usersRepository
    )
    {
        parent::__construct();
    }

    protected function configure():void
    {
        $this
            ->setName('users:create')
            ->setDescription('create new user')
            ->addArgument(
                'first_name',
                InputArgument::REQUIRED,
                'First name'
            )
            ->addArgument(
                'second_name',
                InputArgument::REQUIRED,
                'Last name'
            )
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'Username'
            )
            ->addArgument(
                'password',
                InputArgument::REQUIRED,
                'Password'
            )

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output):int
    {
       $output->writeln('Create user command started');

       $username=$input->getArgument('username');

       if($this->userExists($username)){
           $output->writeln("User already exist");
           return  Command::FAILURE;
       }

       $user = User::createForm(
           $username,
           $input->getArgument('password'),
           new Name(
               $input->getArgument('first_name'),
               $input->getArgument('second_name')
           )
       );
       $this->usersRepository->save($user);

       $output->writeln('User created: '.$user->uuid());

       return Command::SUCCESS;
    }

    private function userExists(string $username):bool
    {
        try{
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException){
            return false;
        }
        return true;
    }


}