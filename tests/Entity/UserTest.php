<?php

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $user = new User();
        $user->setUsername('john_doe');
        $user->setEmail('john@example.com');

        $this->assertEquals('john_doe', $user->getUsername());
        $this->assertEquals('john@example.com', $user->getEmail());
    }
    public function testWhithoutEmail()
    {
        $user = new User();
        $user->setUsername('john_doe');
        $user->setEmail('');
        $regex = '';

        $this->assertEquals('john_doe', $user->getUsername());
        $this->assertSame($regex, $user->getEmail(), "Un mail est obligatoire");
    }
}
