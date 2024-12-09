<?php

namespace App\Command;

use App\Entity\Role;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:user:roles',
    description: 'Manage roles for user',
)]
class UserRolesCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED, 'Username')
            ->addOption('add', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Add role')
            ->addOption('remove', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Add role');
    }

    /**
     * @todo: needs way better input validation.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            $io->error(sprintf('Cannot find user with email %s', $email));

            return Command::FAILURE;
        }

        $roles = $user->getRoles();
        $io->writeln(sprintf('Roles: %s', implode(', ', $roles)));

        $rolesToAdd = [];
        foreach ($input->getOption('add') as $role) {
            $rolesToAdd[] = Role::from($role)->value;
        }

        $rolesToRemove = [];
        foreach ($input->getOption('remove') as $role) {
            $rolesToRemove[] = Role::from($role)->value;
        }

        $roles = array_diff(
            array_merge($roles, $rolesToAdd),
            $rolesToRemove
        );

        $user->setRoles($roles);
        $this->userRepository->save($user, true);

        $io->writeln(sprintf('Roles: %s', implode(', ', $roles)));

        return Command::SUCCESS;
    }
}
