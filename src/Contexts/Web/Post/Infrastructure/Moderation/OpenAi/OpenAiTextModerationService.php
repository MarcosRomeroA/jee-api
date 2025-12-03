<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Infrastructure\Moderation\OpenAi;

use App\Contexts\Shared\Domain\Moderation\ModerationReason;
use App\Contexts\Web\Post\Domain\Moderation\TextModerationService;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class OpenAiTextModerationService implements TextModerationService
{
    private const API_URL = 'https://api.openai.com/v1/moderations';

    public function __construct(
        private HttpClientInterface $httpClient,
        private string $apiKey,
        private LoggerInterface $logger,
    ) {
    }

    public function moderate(string $text): ?ModerationReason
    {
        if (empty(trim($text))) {
            return null;
        }

        try {
            $response = $this->httpClient->request('POST', self::API_URL, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'input' => $text,
                ],
            ]);

            $data = $response->toArray();

            if (empty($data['results'])) {
                return null;
            }

            $result = $data['results'][0];

            if (!$result['flagged']) {
                return null;
            }

            return $this->mapCategoriesToModerationReason($result['categories']);
        } catch (\Throwable $e) {
            $this->logger->error('OpenAI Moderation API error: ' . $e->getMessage(), [
                'text_length' => strlen($text),
                'exception' => $e,
            ]);

            return null;
        }
    }

    private function mapCategoriesToModerationReason(array $categories): ModerationReason
    {
        // Priority order for mapping OpenAI categories to ModerationReason
        if ($categories['sexual'] ?? false) {
            return ModerationReason::SEXUAL_CONTENT;
        }

        if ($categories['sexual/minors'] ?? false) {
            return ModerationReason::SEXUAL_CONTENT;
        }

        if ($categories['hate'] ?? false) {
            return ModerationReason::HATE_SPEECH;
        }

        if ($categories['hate/threatening'] ?? false) {
            return ModerationReason::HATE_SPEECH;
        }

        if ($categories['harassment'] ?? false) {
            return ModerationReason::HARASSMENT;
        }

        if ($categories['harassment/threatening'] ?? false) {
            return ModerationReason::HARASSMENT;
        }

        if ($categories['violence'] ?? false) {
            return ModerationReason::VIOLENCE;
        }

        if ($categories['violence/graphic'] ?? false) {
            return ModerationReason::VIOLENCE;
        }

        if ($categories['self-harm'] ?? false) {
            return ModerationReason::VIOLENCE;
        }

        if ($categories['self-harm/intent'] ?? false) {
            return ModerationReason::VIOLENCE;
        }

        if ($categories['self-harm/instructions'] ?? false) {
            return ModerationReason::VIOLENCE;
        }

        return ModerationReason::INAPPROPRIATE_CONTENT;
    }
}
