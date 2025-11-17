<?php

namespace App\Command;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Post\Application\Create\PostCreator;
use App\Contexts\Web\Post\Domain\ValueObject\BodyValue;
use App\Contexts\Web\User\Domain\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:create-test-post',
    description: 'Creates a test post to trigger async event processing',
)]
class CreateTestPostCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PostCreator $postCreator
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->userRepository->searchAll();

        if (empty($users)) {
            $output->writeln('<error>No users found in database</error>');
            return Command::FAILURE;
        }

        $postId = Uuid::random();
        $body = new BodyValue('Test post for RabbitMQ with #testing #rabbitmq #async');

        ($this->postCreator)(
            $postId,
            $body,
            $users[0],
            [],
            null
        );

        $output->writeln('<info>Post created successfully!</info>');
        $output->writeln('Post ID: ' . $postId->value());
        $output->writeln('Body: ' . $body->value());
        $output->writeln('<comment>Event published to RabbitMQ. Check workers for processing.</comment>');

        return Command::SUCCESS;
    }
}
