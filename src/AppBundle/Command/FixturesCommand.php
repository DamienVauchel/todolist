<?php

namespace AppBundle\Command;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class FixturesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('todo:fixtures')
            ->setDescription('Rempli la base de données avec des données d\'exemple');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $path = $this->getContainer()->get('kernel');

        $users = Yaml::parse(file_get_contents($path->locateResource('@AppBundle/Resources/command/users.yml'), true));
        $tasks = Yaml::parse(file_get_contents($path->locateResource('@AppBundle/Resources/command/tasks.yml'), true));

        foreach($users as $item)
        {
            $user = new User();
            $user->setUsername($item['username']);
            $user->setPassword($item['password']);
            $user->setEmail($item['email']);
            $user->setRoles($item['roles']);

            $em->persist($user);
        }
        $em->flush();

        foreach($tasks as $item)
        {
            $task = new Task();
            $task->setTitle($item['title']);
            $task->setContent($item['content']);
            $task->setIsDone($item['isDone']);
            $task->setCreatedAt(new \DateTime());

            $user_id = $item['user_id'];
            $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $user_id));
            $task->setUser($user);

            $em->persist($task);
        }
        $em->flush();

        $output->writeln("Your datas have been successfully added to database");
    }
}