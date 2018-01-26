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
    private $authUser;
    private $token;

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

        $this->token = new UsernamePasswordToken('authUser', null, $firewallContext, array('ROLE_USER'));
        $session->set('_security_'.$firewallContext, serialize($this->token));
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

        $this->authUser = new User();
        $this->authUser->setUsername('authUser');
        $this->authUser->setPassword('1234');
        $this->authUser->setEmail('auth@example.com');
        $this->authUser->setRoles('ROLE_USER');

        $this->em->persist($this->authUser);
        $this->em->flush();

        $this->task = new Task();
        $this->task->setTitle('Test titre');
        $this->task->setContent('Test contenu');
        $this->task->setUser($this->user);
        $this->task->setIsDone(false);

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

    /**
     * Test if task is well marked as done if undone
     */
    public function testIsMarkedDoneIfUndone()
    {
        $this->addTestFixtures();
        $id = $this->task->getId();
        $title = $this->task->getTitle();
        $this->logInAsUser();
        $this->client->request('GET', '/tasks/'.$id.'/toggle');

        $isDone = $this->task->getIsDone();

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertEquals(302, $statusCode);

        $crawler = $this->client->followRedirect();
        $response = $this->client->getResponse();
        $statusCode = $response->getStatusCode();

        $this->assertEquals(200, $statusCode);
        $this->assertEquals(true, $isDone);
        $this->assertContains('Superbe! La tâche '.$title.' a bien été marquée comme faite.', $crawler->filter('div.alert.alert-success')->text());
    }

    /**
     * Test if task is well marked as done if undone
     */
    public function testIsMarkedUndoneIfDone()
    {
        $this->addTestFixtures();
        $id = $this->task->getId();
        $title = $this->task->getTitle();
        $this->task->setIsDone(true);
        $this->logInAsUser();
        $this->client->request('GET', '/tasks/'.$id.'/toggle');

        $isDone = $this->task->getIsDone();

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertEquals(302, $statusCode);

        $crawler = $this->client->followRedirect();
        $response = $this->client->getResponse();
        $statusCode = $response->getStatusCode();

        $this->assertEquals(200, $statusCode);
        $this->assertEquals(false, $isDone);
        $this->assertContains('Superbe! La tâche '.$title.' a bien été marquée comme à faire.', $crawler->filter('div.alert.alert-success')->text());
    }

    /**
     * Test if task is not deleted when authenticated used isn't its author
     */
    public function testDeleteIfNotAuthor()
    {
        $this->addTestFixtures();
        $id = $this->task->getId();
        $this->logInAsUser();

        $this->client->request('GET', '/tasks/'.$id.'/delete');

        $title = $this->task->getTitle();

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertEquals(302, $statusCode);

        $crawler = $this->client->followRedirect();
        $response = $this->client->getResponse();
        $statusCode = $response->getStatusCode();

        $this->assertEquals(200, $statusCode);
        $this->assertContains('Oops! La tâche '.$title.' n\'a pas été supprimée car vous ne l\'avez pas écrite.', $crawler->filter('div.alert.alert-danger')->text());
    }

    /**
     * Test if task is well deleted when authenticated used is its author
     */
    public function testDeleteIfAuthor()
    {
        $this->addTestFixtures();
        $id = $this->task->getId();
        $this->logInAsUser();

        $this->task->setUser($this->authUser);
        $this->client->request('GET', '/tasks/'.$id.'/delete');

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertEquals(302, $statusCode);

        $crawler = $this->client->followRedirect();
        $response = $this->client->getResponse();
        $statusCode = $response->getStatusCode();

        $this->assertEquals(200, $statusCode);
        $this->assertContains('Superbe! La tâche a bien été supprimée.', $crawler->filter('div.alert.alert-success')->text());
    }

    protected function tearDown()
    {
        $this->em->rollback();

        $this->em->close();
        $this->em = null;
    }
}
