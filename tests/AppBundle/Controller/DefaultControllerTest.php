<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultControllerTest extends WebTestCase
{
    private $client;

    public function setUp()
    {
        $this->client = static::createClient();
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

        $token = new UsernamePasswordToken('user', null, $firewallContext, array('ROLE_USER'));
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    /**
     * Test if Admin can access his buttons and homepage
     */
    public function testHomepageIfAdmin()
    {
        $this->logInAsAdmin();
        $crawler = $this->client->request('GET', '/');

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertEquals(200, $statusCode);
        $this->assertSame('Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !', $crawler->filter('h1')->text());
        $this->assertSame('To Do List app', $crawler->filter('a')->text());
        $this->assertSame('Créer un utilisateur', $crawler->filter('.btn-primary')->text());
        $this->assertSame('Gérer les utilisateurs', $crawler->filter('.btn-info')->text());
        $this->assertSame('Se déconnecter', $crawler->filter('.btn-danger')->text());
    }

    /**
     * Test if User can access homepage
     */
    public function testHomepageIfUser()
    {
        $this->logInAsUser();
        $crawler = $this->client->request('GET', '/');

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertEquals(200, $statusCode);
        $this->assertSame('Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !', $crawler->filter('h1')->text());
        $this->assertSame('To Do List app', $crawler->filter('a')->text());
        $this->assertSame('Se déconnecter', $crawler->filter('.btn-danger')->text());
    }

    /**
     * Test if user who is not logged in is redirected to /login
     */
    public function testHomepageIfNotLoggedIn()
    {
        $this->client->request('GET', '/');

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertEquals(302, $statusCode);
    }
}
