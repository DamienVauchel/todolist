<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AdminCreationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('todo:admin-create')
            ->setDescription('Permet de créer un admin')
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the user')
            ->addArgument('password', InputArgument::REQUIRED, 'The password for the user')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Admin Creator',
            '======================================================='
        ]);

        $output->writeln('Username: '.$input->getArgument('username'));
        $output->writeln('Password: '.$input->getArgument('password'));
        $output->writeln('Email: '.$input->getArgument('email'));

        $userManager = $this->getContainer()->get('user_manager');
        $userManager->createAdmin($input->getArgument('username'), $input->getArgument('password'), $input->getArgument('email'));

        $output->writeln("Your admin has been created");
    }
}