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

    public function loadSubscriptions(ObjectManager $manager): void
    {
        $subscriptions = [
            [
                'title' => 'Free',
                'description' => 'Abonnement GRATUIT',
                'pdfLimit' => 3,
                'price' => 0,
                'media' => '',
            ],
            [
                'title' => 'Premium',
                'description' => 'Abonnement PREMIUM',
                'pdfLimit' => 10,
                'price' => 2,
                'media' => '',
            ],
            [
                'title' => 'Pro',
                'description' => 'Abonnement PRO',
                'pdfLimit' => 20,
                'price' => 10,
                'media' => '',
            ],
        ];

        foreach ($subscriptions as $data) {
            $subscription = new Subscription();
            $subscription->setTitle($data['title']);
            $subscription->setDescription($data['description']);
            $subscription->setPdfLimit($data['pdfLimit']);
            $subscription->setPrice($data['price']);
            $subscription->setMedia($data['media']);
            $manager->persist($subscription);
        }
    }
}
