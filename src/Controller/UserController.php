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

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserPageController',
        ]);
    }
}
