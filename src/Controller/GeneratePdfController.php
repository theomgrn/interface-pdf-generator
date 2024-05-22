<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Service\Gotenberg;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class GeneratePdfController extends AbstractController
{
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
     * Soumet le formulaire et redirige vers la vue PDF
     *
     * @Route('/generate/pdf/submit', name: 'app_submit_pdf', methods: ['POST'])
     */
    #[Route('/generate/pdf/submit', name: 'app_submit_pdf', methods: ['POST'])]
    public function submit(Request $request): RedirectResponse
    {
        // Récupère l'URL depuis le formulaire soumis
        $url = $request->request->get('url');

        // Redirige vers la route app_view_pdf avec l'URL en paramètre
        return $this->redirectToRoute('app_view_pdf', ['url' => $url]);
    }

    /**
     * Génère un PDF à partir d'une URL et l'affiche dans le navigateur
     *
     * @Route('/generate/pdf/view', name: 'app_view_pdf')
     *
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/generate/pdf/view', name: 'app_view_pdf')]
    public function view(Request $request, Gotenberg $gotenberg): Response
    {
        // Récupère l'URL depuis la requête
        $url = $request->query->get('url');

        // Convertit l'URL en PDF
        $pdfContent = $gotenberg->convertUrlToPdf($url);

        // Retourne le PDF en tant que réponse HTTP
        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="document.pdf"',
        ]);
    }
}
