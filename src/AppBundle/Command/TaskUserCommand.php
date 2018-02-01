<?php

namespace AppBundle\Command;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TaskUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('todo:task:anon')
            ->setDescription('All the tasks which have no user linked have now an \'anonyme\' one');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->getContainer()->get('doctrine')->getManager();

        $tasks = $entityManager->getRepository('AppBundle:Task')->findAll();

        $anonymeUser = new User();
        $anonymeUser->setUsername('anonyme');
        $anonymeUser->setPassword('anon');
        $anonymeUser->setEmail('anon@fixture.com');
        $anonymeUser->setRoles('ROLE_USER');

        $entityManager->persist($anonymeUser);
        $entityManager->flush();

        foreach ($tasks as $task)
        {
            if ($task->getUser() === null)
            {
                $task->setUser($anonymeUser);
            }
        }
        $entityManager->flush();

        $output->writeln("Your tasks without users have been linked to 'anonyme'");
    }

}