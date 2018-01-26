<?php

namespace AppBundle\Services;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FindUser
{
    private $tokenStorage;
    private $em;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $em)
    {
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
    }

    /**
     * Find the authenticated user in the db
     *
     * @return User
     */
    public function findUser()
    {
        $user_username = $this->tokenStorage
            ->getToken()
            ->getUsername();

        $user = $this->em
            ->getRepository('AppBundle:User')
            ->findOneBy(array('username' => $user_username));

        return $user;
    }
}