<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\User;
use Tests\AppBundle\Framework\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginAction()
    {
        $user = new User();
        $user->setUsername('user');
        $password = '1234';
        $encodedPassword = $this->container->get('security.password_encoder')->encodePassword($user, $password);
        $user->setPassword($encodedPassword);

        $user->setRoles(array('ROLE_USER'));
        $user->setEmail('userTestLogin@test.com');

        $this->em->persist($user);
        $this->em->flush();

        $crawler = $this->client->request('GET', '/login');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('form.form')->count());

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = $user->getUsername();
        $form['_password'] = $password;

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertSame(1, $crawler->filter('html:contains("Bienvenue sur Todo List")')->count());

    }

    public function testBadCredentials()
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('form.form')->count());

        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username'] = 'bad';
        $form['_password'] = 'credentials';

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertSame(1, $crawler->filter('div.alert.alert-danger:contains("Invalid credentials")')->count());
    }

    public function testLogoutCheck()
    {
        $this->logInAsAdmin();
        $this->client->request('GET', '/logout');
        $this->client->followRedirect();

        $response = $this->client->getResponse();
        $this->assertSame(302, $response->getStatusCode());
        $crawler = $this->client->followRedirect();

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('form.form')->count());
    }
}