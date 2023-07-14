<?php

namespace Starscy\Project\models\Commands\Post;


use Starscy\Project\models\Exceptions\PostNotFoundException;
use Starscy\Project\models\Repositories\Post\PostRepositoryInterface;
use Starscy\Project\models\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DeletePost extends Command
{
    public function __construct(
        private PostRepositoryInterface $postsRepository,
    )
    {
        parent::__construct();
    }

    protected function configure():void
    {
        $this
            ->setName("posts:delete")
            ->setDescription("delete post")
            ->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'UUID of a post to delete',
            )
            ->addOption(
                'check-existance',
                'c',
                InputOption::VALUE_NONE,
                'Check if post actually exists',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output):int
    {
        $question = new ConfirmationQuestion(
            'Delete post [y/n] ? ',
        false
            );
        if(!$this->getHelper('question')
        ->ask($input, $output, $question)){
            return Command::SUCCESS;
        }

        $uuid = new UUID($input->getArgument('uuid'));

        if($input->getOption('check-existance')){
            try{
                $this->postsRepository->get($uuid);
            } catch (PostNotFoundException){
                $output->writeln('This post not found');
                return Command::FAILURE;
            }
        }

        $this->postsRepository->delete($uuid);

        $output->writeln('Post deleted');

        return Command::SUCCESS;
    }
}