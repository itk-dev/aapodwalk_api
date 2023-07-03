<?php

namespace App\Command;

use App\Entity\ApiUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:apiuser:add',
    description: 'Creates API user',
)]
class ApiUserCreateCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('token', InputArgument::REQUIRED, 'Specify token, if not one will be autogenerated')
            ->addArgument('remote', InputArgument::REQUIRED, 'Remote api-key used in callback to OS2Forms');
    }

    /**
     * @todo: needs way better input validation.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new ApiUser();
        $user->setToken($input->getArgument('token'));
        $user->setRemoteApiKey($input->getArgument('remote'));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
