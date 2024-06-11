<?php

namespace App\Controller;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    private SubscriptionRepository $subscriptionRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(SubscriptionRepository $subscriptionRepository, EntityManagerInterface $entityManager)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/subscription', name: 'subscription')]
    public function index(): Response
    {
        $subscriptions = $this->subscriptionRepository->findAll();

        return $this->render('subscription/index.html.twig', [
            'subscriptions' => $subscriptions,
        ]);
    }

    #[Route('/update-subscription', name: 'update_subscription')]
    public function updateSubscription(Request $request): Response
    {
        $subscriptionId = $request->query->get('id');
        $subscription = $this->subscriptionRepository->find($subscriptionId);

        if ($subscription) {
            $user = $this->getUser();
            $user->setSubscriptionId($subscription);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_generate_pdf');
    }

    #[Route('/upgrade-subscription', name: 'upgrade_subscription')]
    public function upgradeSubscription(): Response
    {
        $subscriptions = $this->subscriptionRepository->findAll();

        return $this->render('subscription/limit.html.twig', [
            'subscriptions' => $subscriptions,
        ]);
    }
}
