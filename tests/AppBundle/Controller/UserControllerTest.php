<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\User;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserControllerTest extends WebTestCase
{
    private $client;
    private $container;
    private $em;
    private $user1;
    private $user2;

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->em = $this->container->get('doctrine')->getManager();

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

    /**
     * Simulate a login as an admin (with ROLE_ADMIN)
     */
    private function logInAsAdmin()
    {
        $session = $this->client->getContainer()->get('session');

        $firewallContext = 'main';

        $token = new UsernamePasswordToken('admin', null, $firewallContext, array('ROLE_ADMIN'));
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    /**
     * Simulate a login as an user (with ROLE_USER)
     */
    private function logInAsUser()
    {
        $session = $this->client->getContainer()->get('session');

        $firewallContext = 'main';

        $token = new UsernamePasswordToken('authUser', null, $firewallContext, array('ROLE_USER'));
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
        $this->user1 = new User();
        $this->user1->setUsername('testUser01');
        $this->user1->setPassword('1234');
        $this->user1->setEmail('test01@example.com');
        $this->user1->setRoles('ROLE_USER');

        $this->em->persist($this->user1);
        $this->em->flush();

        $this->user2 = new User();
        $this->user2->setUsername('testUser02');
        $this->user2->setPassword('1234');
        $this->user2->setEmail('test02@example.com');
        $this->user2->setRoles('ROLE_USER');

        $this->em->persist($this->user2);
        $this->em->flush();
    }

    /**
     * Test if user is well redirected if not logged in
     */
    public function testIsRedirectedIfNotLoggedIn()
    {
        $this->addTestFixtures();
        $this->client->request('GET', '/users');

        $response = $this->client->getResponse();

        $statusCode = $response->getStatusCode();
        $this->assertEquals(302, $statusCode);

        $crawler = $this->client->followRedirect();
        $response = $this->client->getResponse();
        $statusCode = $response->getStatusCode();

        $this->assertEquals(200, $statusCode);
        $this->assertContains('Connexion', $crawler->filter('h3')->text());
    }

    /**
     * Test if user has statusCode 403 if has ROLE_USER
     */
    public function testNoAccessForRoleUser()
    {
        $this->addTestFixtures();
        $this->logInAsUser();
        $this->client->request('GET', '/users');

        $response = $this->client->getResponse();

        $statusCode = $response->getStatusCode();
        $this->assertEquals(403, $statusCode);
    }

    /**
     * Test if Admin can access the users list
     */
    public function testIsOnUsersListPage()
    {
        $this->addTestFixtures();
        $this->logInAsAdmin();
        $this->client->request('GET', '/users');

        $response = $this->client->getResponse();
        $responseContent = $response->getContent();

        $statusCode = $response->getStatusCode();
        $this->assertEquals(200, $statusCode);
        $this->assertContains($this->user1->getUsername(), $responseContent);
        $this->assertContains($this->user2->getUsername(), $responseContent);
    }

    /**
     * Test if user is well added by the add form
     */
    public function testCreateUser()
    {
        $this->logInAsAdmin();
        $crawler = $this->client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'userAddTest';
        $form['user[password][first]'] = '1234';
        $form['user[password][second]'] = '1234';
        $form['user[email]'] = 'test@test.com';
        $form['user[roles]'] = 'ROLE_USER';

        $this->client->submit($form);

        $crawler = $this->client->followRedirect();
        $response = $this->client->getResponse();
        $statusCode = $response->getStatusCode();

        $this->assertEquals(200, $statusCode);
        $this->assertContains('Superbe! L\'utilisateur a bien été ajouté.', $crawler->filter('div.alert.alert-success')->text());
    }

    /**
     * Test if user is well modified by the edit form
     */
    public function testUpdateUser()
    {
        $this->addTestFixtures();
        $this->logInAsAdmin();
        $id = $this->user1->getId();

        $crawler = $this->client->request('GET', '/users/'.$id.'/edit');

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'userAddTest';
        $form['user[password][first]'] = '1234';
        $form['user[password][second]'] = '1234';
        $form['user[email]'] = 'test@test.com';
        $form['user[roles]'] = 'ROLE_USER';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();
        $response = $this->client->getResponse();
        $statusCode = $response->getStatusCode();

        $this->assertEquals(200, $statusCode);
        $this->assertContains('Superbe! L\'utilisateur a bien été modifié', $crawler->filter('div.alert.alert-success')->text());
    }

    protected function tearDown()
    {
        $this->em->close();
        $this->em = null;
    }
}