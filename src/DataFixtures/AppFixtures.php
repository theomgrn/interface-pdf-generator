<?php

namespace App\DataFixtures;

use App\Entity\Subscription;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadSubscriptions($manager);
        $manager->flush();
    }

    public function loadSubscriptions(ObjectManager $manager): void {
        $subscription1 = new Subscription();
        $subscription1->setTitle('Free');
        $subscription1->setDescription('Free subscription');
        $subscription1->setPdfLimit(3);
        $subscription1->setPrice(0);
        $subscription1->setMedia('');

        $subscription2 = new Subscription();
        $subscription2->setTitle('Premium');
        $subscription2->setDescription('Premium subscription');
        $subscription2->setPdfLimit(10);
        $subscription2->setPrice(2);
        $subscription2->setMedia('');

        $subscription3 = new Subscription();
        $subscription3->setTitle('Pro');
        $subscription3->setDescription('Pro subscription');
        $subscription3->setPdfLimit(20);
        $subscription3->setPrice(10);
        $subscription3->setMedia('');

        $manager->persist($subscription1);
        $manager->persist($subscription2);
        $manager->persist($subscription3);
    }
}