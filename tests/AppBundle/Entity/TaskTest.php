<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Tests\AppBundle\Framework\WebTestCase;

class TaskTest extends WebTestCase
{
    public function testTask()
    {
        $user = new User();
        $user->hydrate('user', '1234', 'user@user.com', array('ROLE_USER'));
        $this->em->persist($user);
        $this->em->flush();

        $task = new Task();
        $task->setTitle('Test task title');
        $task->setContent('Test task content');
        $task->setUser($user);
        $task->setCreatedAt(New \DateTime('2018-01-12 23:30:00'));
        $this->em->persist($task);
        $this->em->flush();


        $this->assertSame(1, $task->getId());
        $this->assertSame('Test task title', $task->getTitle());
        $this->assertSame('Test task content', $task->getContent());
        $this->assertSame($user, $task->getUser());
        $this->assertFalse($task->isDone());
        $this->assertEquals(New \DateTime('2018-01-12 23:30:00'), $task->getCreatedAt());
    }
}
