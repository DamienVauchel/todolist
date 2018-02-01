<?php

namespace Tests\AppBundle\Command;

use AppBundle\Command\TaskUserCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class TaskUserCommandTest extends KernelTestCase
{
    public function testExectue()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $application->add(new TaskUserCommand());
        $command = $application->find('todo:task:anon');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName()
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('Your tasks without users have been linked to \'anonyme\'', $output);
    }
}