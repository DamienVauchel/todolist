<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TaskControllerTest extends WebTestCase
{
    private $client;
    private $container;
    private $em;
    private $task;
    private $user;

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->em = $this->container->get('doctrine')->getManager();

        $this->em->beginTransaction();
        $this->em->getConnection()->setAutoCommit(false);
    }

    /**
     * Simulate a login as an user (with ROLE_USER)
     */
    private function logInAsUser()
    {
        $session = $this->client->getContainer()->get('session');

        $firewallContext = 'main';

        $token = new UsernamePasswordToken('user', null, $firewallContext, array('ROLE_USER'));
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    /**
     * Add example fixtures
     */
    public function addTestFixtures()
    {
        $this->user = new User();
        $this->user->setUsername('testUser');
        $this->user->setPassword('1234');
        $this->user->setEmail('test@example.com');
        $this->user->setRoles('ROLE_USER');

        $this->em->persist($this->user);
        $this->em->flush();

        $this->task = new Task();
        $this->task->setTitle('Test titre');
        $this->task->setContent('Test contenu');
        $this->task->setUser($this->user);

        $this->em->persist($this->task);
        $this->em->flush();
    }

    /**
     * Test if Admin can access his buttons and homepage
     */
    public function testIsOnTasksListPage()
    {
        $this->addTestFixtures();
        $this->logInAsUser();
        $this->client->request('GET', '/tasks');

        $response = $this->client->getResponse();
        $responseContent = $response->getContent();

        $statusCode = $response->getStatusCode();
        $this->assertEquals(200, $statusCode);
        $this->assertContains($this->task->getTitle(), $responseContent);
        $this->assertContains($this->task->getContent(), $responseContent);
    }

    protected function tearDown()
    {
        $this->em->rollback();

        $this->em->close();
        $this->em = null;
    }
}
