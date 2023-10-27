<?php

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserUnitTest extends TestCase
{
    public function testIsTrue(): void
    {
        $user = new User();

        $user->setEmail('test@email.fr')
            ->setFirstnameIdentifier('firstname_identifier')
            ->setNameIdentifier('name_identifier')
            ->setPseudo('pseudo');

        $this->assertTrue($user->getEmail() === 'test@email.fr');
        $this->assertTrue($user->getFirstnameIdentifier() === 'firstname_identifier');
        $this->assertTrue($user->getNameIdentifier() === 'name_identifier');
        $this->assertTrue($user->getPseudo() === 'pseudo');
    }

    public function testIsFalse(): void
    {
        $user = new User();

        $user->setEmail('test@email.fr')
            ->setFirstnameIdentifier('firstname_identifier')
            ->setNameIdentifier('name_identifier')
            ->setPseudo('pseudo');

        $this->assertFalse($user->getEmail() === 'false@email.fr');
        $this->assertFalse($user->getUserIdentifier() === 'false');
        $this->assertFalse($user->getNameIdentifier() === 'false');
        $this->assertFalse($user->getPseudo() === 'false');
    }

    public function testIsEmpty(): void
    {
        $user = new User();

        $this->assertEmpty($user->getEmail());
        $this->assertEmpty($user->getUserIdentifier());
        $this->assertEmpty($user->getNameIdentifier());
        $this->assertEmpty($user->getPseudo());
    }
}

