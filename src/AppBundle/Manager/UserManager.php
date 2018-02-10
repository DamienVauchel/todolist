<?php

namespace AppBundle\Manager;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class UserManager
{
    private $entityManager;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoder $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
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
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $password);
        $roles = array("ROLE_ADMIN");
        $user->hydrate($username, $encodedPassword, $email, $roles);

        $this->save($user);
    }

    /**
     * Save the User in the database if he doesn't already exist
     *
     * @param User $user
     */
    public function save(User $user)
    {
        if (!$this->userExists($user)) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
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
        $user = $this->entityManager->getRepository(User::class)->findOneBy(array('username' => $user->getUsername()));
        return isset($user) ? true : false;
    }
}