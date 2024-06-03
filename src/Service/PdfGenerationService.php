<?php

namespace App\Service;

use AllowDynamicProperties;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AllowDynamicProperties]
class PdfGenerationService
{
    private string $apiUrl;

    public function __construct(ParameterBagInterface $params)
    {
        $this->apiUrl = $_ENV['SYMFONY_API'];
    }

    /**
     * Convertit une URL en PDF en utilisant le service Gotenberg
     *
     * @param string $url L'URL à convertir en PDF
     * @return string Le contenu du PDF
     *
     * @throws \Exception
     */
    public function fromUrl(string $url): string
    {
        $ch = curl_init();

        $endpoint = $this->apiUrl . 'generate/pdf';
        $postData = json_encode(['url' => $url]);

        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new \Exception('Error: ' . curl_error($ch));
        }

        if ($httpCode !== 200) {
            throw new \Exception('Error: HTTP status code ' . $httpCode);
        }

        curl_close($ch);

        return $response;
    }
}
