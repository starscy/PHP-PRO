<?php

namespace Starscy\Project\models\Commands\FakeData;


use Starscy\Project\models\Blog\Comment;
use Starscy\Project\models\Blog\Post;
use Starscy\Project\models\Person\Name;
use Starscy\Project\models\Repositories\Comment\CommentRepositoryInterface;
use Starscy\Project\models\Repositories\Post\PostRepositoryInterface;
use Starscy\Project\models\Repositories\User\UserRepositoryInterface;
use Starscy\Project\models\User;
use Starscy\Project\models\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDB extends Command
{
    public function __construct(
        private \Faker\Generator $faker,
        private UserRepositoryInterface $usersRepository,
        private PostRepositoryInterface $postsRepository,
        private CommentRepositoryInterface $commentsRepository,
    )
    {
        parent::__construct();
    }

    protected function configure():void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data')
            ->addOption(
                'users-number',
                'u',
                InputOption::VALUE_OPTIONAL,
                'Number of users',
            )
            ->addOption(
                'posts-number',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Number of posts',
            )
            ->addOption(
                'comments-number',
                'm',
                InputOption::VALUE_OPTIONAL,
                'Number of comments',
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output):int
    {
        $users=[];
        $usersCount = $input->getOption('users-number') ?? 1;
        for($i=0;$i<$usersCount;$i++){
            $user=$this->createFakeUser();
            $users[]=$user;
            $output->writeln('Create new user: '. $user->getUsername());
        }

        $posts=[];
        $postsCount = $input->getOption('posts-number') ?? 1;
        foreach ($users as $user){
            for($i=0;$i<$postsCount;$i++){
                $post=$this->createFakePost($user);
                $posts[]=$post;
                $output->writeln('Post created '.$post->getTitle());
            }
        }

        $commentsCount = $input->getOption('comments-number') ?? 1;
        foreach ($users as $user){
            foreach ($posts as $post){
                for($i=0;$i<$commentsCount;$i++){
                    $comment=$this->createFakeComment($post,$user);
                    $output->writeln('Comment created '.$comment->getText());
                }
            }

        }
        return Command::SUCCESS;
    }
    private function createFakeUser():User
    {
        $user = new User(
            UUID::random(),
            $this->faker->username,
            $this->faker->password,
            new Name(
                $this->faker->firstName,
                $this->faker->lastName
            )
        );
        $this->usersRepository->save($user);
        return $user;
    }

    private function createFakePost(User $author):Post
    {
        $post = new Post(
            UUID::random(),
            $author,
            $this->faker->sentence(6, true),
            $this->faker->realText,
        );
        $this->postsRepository->save($post);
        return $post;
    }

    private function createFakeComment ( Post $post,User $author): Comment
    {
        $comment = new Comment(
            UUID::random(),
            $post,
            $author,
            $this->faker->realText
        );
        $this->commentsRepository->save($comment);
        return $comment;
    }
}