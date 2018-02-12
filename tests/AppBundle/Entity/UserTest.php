<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\User;
use Tests\AppBundle\Framework\WebTestCase;

class UserTest extends WebTestCase
{
    public function testUser()
    {
        $user = new User();
        $user->hydrate('user', '1234', 'user@user.com', array('ROLE_USER'));
        $this->em->persist($user);
        $this->em->flush();


        $this->assertSame(1, $user->getId());
        $this->assertSame('user', $user->getUsername());
        $this->assertSame('1234', $user->getPassword());
        $this->assertSame(array('ROLE_USER'), $user->getRoles());
        $this->assertEquals('user@user.com', $user->getEmail());
        $this->assertNull($user->eraseCredentials());

    }
}