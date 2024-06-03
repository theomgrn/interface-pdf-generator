<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;

class HistoryController extends AbstractController
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    #[Route('/history', name: 'history')]
    public function index(): Response
    {
        // Supposons que vous ayez une méthode pour obtenir l'ID de l'utilisateur actuel
        $userId = $this->getUser()->getId();

        // Chemin vers le répertoire des fichiers de l'utilisateur
        $directoryPath = $this->kernel->getProjectDir() . '/public/pdfs/' . $userId;

        // S'assurer que le répertoire existe et est lisible
        if (is_dir($directoryPath)) {
            // Récupérer la liste des fichiers
            $files = array_diff(scandir($directoryPath), ['.', '..']);

            // Optionnel: transformer le chemin relatif en URL publiable
            $fileUrls = [];
            $displayedUrls = [];
            foreach ($files as $file) {
                $fileUrls[] = $this->generateUrl('kernel_absolute_path', ['userId' => $userId, 'fileName' => $file]);
                $displayedUrls[] = $userId . '/' . $file;
            }
        } else {
            // Gérer le cas où le répertoire n'existe pas
            $fileUrls = [];
            $displayedUrls = [];
        }

        return $this->render('history/index.html.twig', [
            'controller_name' => 'HistoryController',
            'files' => $fileUrls,
            'displayedUrls' => $displayedUrls
        ]);
    }

    #[Route('/history/absolute/{userId}/{fileName}', name: 'kernel_absolute_path')]
    public function getKernelAbsolutePath(string $userId, string $fileName): Response
    {
        $directoryPath = $this->kernel->getProjectDir() . '/public/pdfs/' . $userId . '/' . $fileName;

        // S'assurer que le fichier existe
        if (!file_exists($directoryPath)) {
            throw $this->createNotFoundException('Le fichier demandé n\'existe pas');
        }

        // Retourner le fichier
        return $this->file($directoryPath);
    }
}
