<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findAllWithout($username)
    {
        $query = $this->createQueryBuilder('u')
            ->where('u.username != :username')
            ->setParameter('username', $username)
            ->getQuery();

        return $query->getArrayResult();
    }
}