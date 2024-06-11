<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class UserController extends AbstractController
{
    #[Route('/userpage', name: 'user_page')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $maxPdfLimit = $user->getSubscriptionId()->getPdfLimit();
        $currentPdfCount = $user->getPdfLimit();

        return $this->render('user/index.html.twig', [
            'maxPdfLimit' => $maxPdfLimit,
            'currentPdfCount' => $currentPdfCount,
            'controller_name' => 'UserPageController',
        ]);
    }
}
