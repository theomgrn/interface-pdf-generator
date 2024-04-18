<?php
namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGetterAndSetter()
    {
        $user = new User();
        $email = 'test@test.com';
        $firstName = 'John';
        $lastName = 'Doe';
        $password = 'test';
        $roles = ['ROLE_USER'];
        $subscription_id = null;
        $subscriptionEndAt = new \DateTimeImmutable();
        $createdAt = new \DateTimeImmutable();
        $updatedAt = new \DateTimeImmutable();

        // Utilisation des setters
        $user->setEmail($email);
        $user->setFirstname($firstName);
        $user->setLastname($lastName);
        $user->setPassword($password);
        $user->setRoles($roles);
        $user->setSubscriptionId($subscription_id);
        $user->setSubscriptionEndAt($subscriptionEndAt);
        $user->setCreatedAt($createdAt);
        $user->setUpdatedAt($updatedAt);

        // Vérification des getters
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($firstName, $user->getFirstname());
        $this->assertEquals($lastName, $user->getLastname());
        $this->assertEquals($password, $user->getPassword());
        $this->assertEquals($roles, $user->getRoles());
        $this->assertEquals($subscription_id, $user->getSubscriptionId());
        $this->assertEquals($subscriptionEndAt, $user->getSubscriptionEndAt());
        $this->assertEquals($createdAt, $user->getCreatedAt());
        $this->assertEquals($updatedAt, $user->getUpdatedAt());
    }
}
