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
        $currentUserId = $this->getUser()->getId();

        $userFilesDirectory = $this->kernelInterface->getProjectDir() . '/public/pdfs/' . $currentUserId;

        if (is_dir($userFilesDirectory)) {
            $userFiles = array_diff(scandir($userFilesDirectory), ['.', '..']);

            $fileUrls = [];
            $relativePaths = [];
            foreach ($userFiles as $fileName) {
                $fileUrls[] = $this->generateUrl('document_absolute_path', ['userId' => $currentUserId, 'fileName' => $fileName]);
                $relativePaths[] = $currentUserId . '/' . $fileName;
            }
        } else {
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

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Le fichier demandé n\'existe pas');
        }

        return $this->file($filePath);
    }
}
