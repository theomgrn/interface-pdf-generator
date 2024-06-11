<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;

class HistoryController extends AbstractController
{
    private KernelInterface $kernelInterface;

    public function __construct(KernelInterface $kernelInterface)
    {
        $this->kernelInterface = $kernelInterface;
    }

    #[Route('/history', name: 'history')]
    public function index(): Response
    {
        // Supposons que vous ayez une méthode pour obtenir l'ID de l'utilisateur actuel
        $currentUserId = $this->getUser()->getId();

        // Chemin vers le répertoire des fichiers de l'utilisateur
        $userFilesDirectory = $this->kernelInterface->getProjectDir() . '/public/pdfs/' . $currentUserId;

        // S'assurer que le répertoire existe et est lisible
        if (is_dir($userFilesDirectory)) {
            // Récupérer la liste des fichiers
            $userFiles = array_diff(scandir($userFilesDirectory), ['.', '..']);

            // Optionnel: transformer le chemin relatif en URL publiable
            $fileUrls = [];
            $relativePaths = [];
            foreach ($userFiles as $fileName) {
                $fileUrls[] = $this->generateUrl('document_absolute_path', ['userId' => $currentUserId, 'fileName' => $fileName]);
                $relativePaths[] = $currentUserId . '/' . $fileName;
            }
        } else {
            // Gérer le cas où le répertoire n'existe pas
            $fileUrls = [];
            $relativePaths = [];
        }

        return $this->render('history/index.html.twig', [
            'controller_name' => 'DocumentHistoryController',
            'fileUrls' => $fileUrls,
            'relativePaths' => $relativePaths
        ]);
    }

    #[Route('/history/absolute/{userId}/{fileName}', name: 'document_absolute_path')]
    public function getAbsoluteFilePath(string $userId, string $fileName): Response
    {
        $filePath = $this->kernelInterface->getProjectDir() . '/public/pdfs/' . $userId . '/' . $fileName;

        // S'assurer que le fichier existe
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Le fichier demandé n\'existe pas');
        }

        // Retourner le fichier
        return $this->file($filePath);
    }
}
