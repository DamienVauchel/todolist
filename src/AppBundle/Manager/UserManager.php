<?php

namespace AppBundle\Manager;


use AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class UserManager
{
    private $em;
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
    }

    /**
     * Create the user by his username, password and email
     *
     * @param $username
     * @param $password
     * @param $email
     */
    public function createAdmin($username, $password, $email)
    {
        $user = new User();
        $password = $this->container->get('security.password_encoder')->encodePassword($user, $user->getPassword());
        $user->setUsername($username);
        $user->setPassword($password);
        $user->setEmail($email);
        $user->setRoles(array("ROLE_ADMIN"));

        $this->save($user);
    }

    /**
     * Save the User in the database if he doesn't already exist
     *
     * @param User $user
     */
    public function save(User $user)
    {
        if (!$this->userExists($user))
        {
            $this->em->persist($user);
            $this->em->flush();
        }
    }

    /**
     * Verify if user exists by his username
     *
     * @param User $user
     * @return bool
     */
    public function userExists(User $user)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(array('username' => $user->getUsername()));
        return isset($user) ? true : false;
    }
}