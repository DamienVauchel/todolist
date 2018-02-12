<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\User;
use Tests\AppBundle\Framework\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private $user1;
    private $user2;

    /**
     * Add example fixtures
     */
    public function addTestFixtures()
    {
        $this->user1 = new User();
        $this->user1->hydrate('testUser01', '1234', 'test01@example.com', array('ROLE_USER'));
        $this->em->persist($this->user1);
        $this->em->flush();

        $this->user2 = new User();
        $this->user2->hydrate('testUser02', '1234', 'test02@example.com', array('ROLE_USER'));
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
}