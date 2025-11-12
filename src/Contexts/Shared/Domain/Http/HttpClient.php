<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Http;

/**
 * Cliente HTTP para hacer peticiones a APIs externas
 * Abstracción para desacoplar de implementación específica (Guzzle, Symfony HTTP Client, etc.)
 */
interface HttpClient
{
    /**
     * Realiza una petición GET
     *
     * @param string $url URL completa del endpoint
     * @param array $headers Headers adicionales
     * @param array $queryParams Query parameters
     * @return HttpResponse
     */
    public function get(string $url, array $headers = [], array $queryParams = []): HttpResponse;

    /**
     * Realiza una petición POST
     *
     * @param string $url URL completa del endpoint
     * @param array $body Cuerpo de la petición
     * @param array $headers Headers adicionales
     * @return HttpResponse
     */
    public function post(string $url, array $body = [], array $headers = []): HttpResponse;
}

