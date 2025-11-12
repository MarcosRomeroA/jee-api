<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Http;

use App\Contexts\Shared\Domain\Http\HttpClient;
use App\Contexts\Shared\Domain\Http\HttpResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Implementación de HttpClient usando Symfony HTTP Client
 */
final class SymfonyHttpClient implements HttpClient
{
    public function __construct(
        private readonly HttpClientInterface $client
    ) {
    }

    public function get(string $url, array $headers = [], array $queryParams = []): HttpResponse
    {
        try {
            $response = $this->client->request('GET', $url, [
                'headers' => $headers,
                'query' => $queryParams,
                'timeout' => 10,
            ]);

            return new HttpResponse(
                $response->getStatusCode(),
                $response->toArray(false), // false = no lanzar excepción en error HTTP
                $response->getHeaders(false)
            );
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('HTTP Transport Error: ' . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            throw new \RuntimeException('HTTP Client Error: ' . $e->getMessage(), 0, $e);
        }
    }

    public function post(string $url, array $body = [], array $headers = []): HttpResponse
    {
        try {
            $response = $this->client->request('POST', $url, [
                'headers' => array_merge(['Content-Type' => 'application/json'], $headers),
                'json' => $body,
                'timeout' => 10,
            ]);

            return new HttpResponse(
                $response->getStatusCode(),
                $response->toArray(false),
                $response->getHeaders(false)
            );
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('HTTP Transport Error: ' . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            throw new \RuntimeException('HTTP Client Error: ' . $e->getMessage(), 0, $e);
        }
    }
}

