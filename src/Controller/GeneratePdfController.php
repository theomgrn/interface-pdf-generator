<?php

namespace App\Controller;

use App\Entity\Pdf;
use App\Service\PdfGenerationService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class GeneratePdfController extends AbstractController
{
    private string $apiUrl;
    private PdfGenerationService $pdfGenerationService;
    private Filesystem $filesystem;
    private EntityManagerInterface $entityManager;

    public function __construct(
        ParameterBagInterface $params,
        PdfGenerationService $pdfGenerationService,
        Filesystem $filesystem,
        EntityManagerInterface $entityManager
    ) {
        $this->pdfGenerationService = $pdfGenerationService;
        $this->apiUrl = $params->get('symfony_api');
        $this->filesystem = $filesystem;
        $this->entityManager = $entityManager;
    }

    #[Route('/generate/pdf', name: 'app_generate_pdf')]
    public function form(): Response
    {
        $user = $this->getUser();
        $maxPdfLimit = $user->getSubscriptionId()->getPdfLimit();
        $currentPdfCount = $user->getPdfLimit();

        return $this->render('generatePdf/index.html.twig', [
            'maxPdfLimit' => $maxPdfLimit,
            'currentPdfCount' => $currentPdfCount,
        ]);
    }

    #[Route('/generate/pdf/submit', name: 'app_submit_pdf', methods: ['POST'])]
    public function submit(Request $request): Response
    {
        $user = $this->getUser();
        if ($user->getPdfLimit() >= $user->getSubscriptionId()->getPdfLimit()) {
            return $this->redirectToRoute('upgrade_subscription');
        }

        $url = $request->request->get('url');

        try {
            $pdfContent = $this->pdfGenerationService->fromUrl($url);

            $currentDate = new DateTimeImmutable();
            $fileName = $currentDate->getTimestamp() . '.pdf';
            $publicPath = $this->getParameter('kernel.project_dir') . '/public/pdfs/' . $user->getId() . '/' . $fileName;

            $this->filesystem->mkdir(dirname($publicPath));
            $this->filesystem->dumpFile($publicPath, $pdfContent);

            // Créer un nouvel objet Pdf et le persister
            $pdf = new Pdf();
            $pdf->setTitle($fileName);
            $pdf->setCreatedAt($currentDate);
            $pdf->setUserId($user);

            $this->entityManager->persist($pdf);

            // Mettre à jour le pdfLimit de l'utilisateur
            $user->setPdfLimit($user->getPdfLimit() + 1);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return new Response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
        } catch (\Exception $e) {
            return new Response('Error generating PDF: ' . $e->getMessage(), 500);
        }
    }
}
