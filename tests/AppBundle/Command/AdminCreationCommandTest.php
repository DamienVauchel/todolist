<?php

namespace Tests\AppBundle\Command;

use AppBundle\Command\AdminCreationCommand;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class AdminCreationCommandTest extends KernelTestCase
{
    private $em;
    protected static $kernel;

    protected function setUp()
    {
        static::$kernel = self::bootKernel();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();

        static $metadatas = null;
        if (is_null($metadatas))
        {
            $metadatas = $this->em->getMetadataFactory()->getAllMetadata();
        }

        $schemaTool = new SchemaTool($this->em);
        $schemaTool->dropDatabase();

        if (!empty($metadatas))
        {
            $schemaTool->createSchema($metadatas);
        }
    }

    public function testExectue()
    {
        $application = new Application(static::$kernel);

        $application->add(new AdminCreationCommand());
        $command = $application->find('todo:admin-create');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),

            'username' => "damien",
            'password' => "admin",
            'email' => "damienadmin@admin.com"
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains("Your admin has been created", $output);
    }

    protected function tearDown()
    {
        $this->em->close();
        $this->em = null;
    }

}