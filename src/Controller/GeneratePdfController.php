<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Service\PdfGenerationService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

#[AllowDynamicProperties] class GeneratePdfController extends AbstractController
{
    private string $apiUrl;
    private PdfGenerationService $service;
    private Filesystem $filesystem;

    public function __construct(ParameterBagInterface $params, PdfGenerationService $service, Filesystem $filesystem, EntityManagerInterface $entityManager)
    {
        $this->service = $service;
        $this->apiUrl = $params->get('symfony_api');
        $this->filesystem = $filesystem;
        $this->entityManager = $entityManager;
    }

    /**
     * Affiche le formulaire de génération de PDF
     *
     * @Route('/generate/pdf', name: 'app_generate_pdf')
     */
    #[Route('/generate/pdf', name: 'app_generate_pdf')]
    public function form(): Response
    {
        return $this->render('generatePdf/index.html.twig');
    }

    /**
     * Soumet le formulaire et génère le PDF
     *
     * @Route('/generate/pdf/submit', name: 'app_submit_pdf', methods: ['POST'])]
     */
    #[Route('/generate/pdf/submit', name: 'app_submit_pdf', methods: ['POST'])]
    public function submit(Request $request): Response
    {
        // Si le formulaire est soumis et valide
        $user = $this->getUser();
        if($user->getPdfLimit() >= $user->getSubscriptionId()->getPdfLimit()) {
            return $this->redirectToRoute('upgrade_subscription');
        }
        $user->setPdfLimit($user->getPdfLimit() + 1);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

// Récupère l'URL depuis le formulaire soumis
        $url = $request->request->get('url');

        try {
// Utilise le service pour obtenir le PDF
            $pdf = $this->service->fromUrl($url);

            $currentDate = new DateTime();
            $user = $this->getUser();
            $fileName = $currentDate->getTimestamp() . '.pdf';
            $publicPath = $this->getParameter('kernel.project_dir') . '/public/pdfs/' . $user->getId() . '/' . $fileName;

// Assure que le répertoire existe
            $this->filesystem->mkdir(dirname($publicPath));

// Sauvegarde le fichier PDF
            $this->filesystem->dumpFile($publicPath, $pdf);


// Retourne le PDF en tant que réponse HTTP
            return new Response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="document.pdf"',
            ]);

        } catch (\Exception $e) {
            return new Response('Error generating PDF: ' . $e->getMessage(), 500);
        }
    }
}
