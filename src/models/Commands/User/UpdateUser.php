<?php
namespace Starscy\Project\models\Commands\User;


use Starscy\Project\models\Person\Name;
use Starscy\Project\models\Repositories\User\UserRepositoryInterface;
use Starscy\Project\models\User;
use Starscy\Project\models\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateUser extends Command
{
    public function __construct(
        private UserRepositoryInterface $usersRepository,
    )
    {
        parent::__construct();
    }

    protected  function configure():void
    {
        $this
            ->setName('users:update')
            ->setDescription('Update information about user')
            ->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'UUID of a user to update',
            )
            ->addOption(
                'first_name',
                'f',
                InputOption::VALUE_OPTIONAL,
                'First name',
            )
            ->addOption(
                'second_name',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Last name'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output):int
    {
        $firstName = $input->getOption('first_name');
        $lastName = $input->getOption('second_name');

        if(empty($firstName) && empty($lastName)){
            $output->writeln('Nothing to update');
            return Command::SUCCESS;
        }

        $uuid = new UUID($input->getArgument('uuid'));

        $user = $this->usersRepository->get($uuid);

        $updateName = new Name(
            empty($firstName) ? $user->getName()->getFirst() : $firstName,
        empty($lastName) ? $user->getName()->getSecond() : $lastName,
        );

        $updatedUser = new User(
            $uuid,
            $user->getUsername(),
            $user->hashedPassword(),
            $updateName,
        );

        $this->usersRepository->save($updatedUser);

        $output->writeln('User updated: '.$uuid);

        return  Command::SUCCESS;
    }
}

