<?php

namespace Tests\AppBundle\Command;

use AppBundle\Command\TaskUserCommand;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class FixturesCommandTest extends KernelTestCase
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

        $application->add(new TaskUserCommand());
        $command = $application->find('todo:fixtures');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName()
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('Your datas have been successfully added to database', $output);
    }

    protected function tearDown()
    {
        $this->em->close();
        $this->em = null;
    }
}