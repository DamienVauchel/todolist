<?php

namespace Tests\AppBundle\Framework;

use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class WebTestCase extends BaseWebTestCase
{
    protected $client;
    protected $container;
    protected $em;
    protected $token;

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
     * Simulate a login as an user (with ROLE_USER)
     */
    protected function logInAsUser()
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
     * Simulate a login as an admin (with ROLE_ADMIN)
     */
    protected function logInAsAdmin()
    {
        $session = $this->client->getContainer()->get('session');

        $firewallContext = 'main';

        $token = new UsernamePasswordToken('admin', null, $firewallContext, array('ROLE_ADMIN'));
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    protected function tearDown()
    {
        $this->em->close();
        $this->em = null;
    }
}