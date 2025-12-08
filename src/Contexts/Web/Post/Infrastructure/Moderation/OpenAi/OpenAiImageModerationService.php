<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Infrastructure\Moderation\OpenAi;

use App\Contexts\Shared\Domain\Moderation\ModerationReason;
use App\Contexts\Web\Post\Domain\Moderation\ImageModerationService;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class OpenAiImageModerationService implements ImageModerationService
{
    private const API_URL = 'https://api.openai.com/v1/chat/completions';

    public function __construct(
        private HttpClientInterface $httpClient,
        private string $apiKey,
        private LoggerInterface $logger,
    ) {
    }

    public function moderate(string $imageUrl): ?ModerationReason
    {
        if (empty($imageUrl)) {
            return null;
        }

        try {
            $response = $this->httpClient->request('POST', self::API_URL, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a content moderation assistant. Analyze images and respond ONLY with a JSON object. No other text.',
                        ],
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => 'Analyze this image for inappropriate content. Respond with a JSON object with two fields: "flagged" (boolean) and "category" (string, one of: "sexual", "violence", "hate", "harassment", "safe"). Only flag content that is clearly inappropriate.',
                                ],
                                [
                                    'type' => 'image_url',
                                    'image_url' => [
                                        'url' => $imageUrl,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'max_tokens' => 100,
                ],
            ]);

            $data = $response->toArray();

            $content = $data['choices'][0]['message']['content'] ?? '';

            $this->logger->debug('OpenAI Image Moderation response', [
                'image_url' => $imageUrl,
                'raw_response' => $content,
            ]);

            $result = $this->parseResponse($content);

            $this->logger->info('Image moderation completed', [
                'image_url' => $imageUrl,
                'flagged' => $result !== null,
                'reason' => $result?->value,
            ]);

            return $result;
        } catch (\Throwable $e) {
            $this->logger->error('OpenAI Image Moderation API error', [
                'image_url' => $imageUrl,
                'error' => $e->getMessage(),
            ]);

            // Re-throw to trigger retry - don't silently pass moderation on API errors
            throw $e;
        }
    }

    private function parseResponse(string $content): ?ModerationReason
    {
        $content = trim($content);
        $content = preg_replace('/^```json\s*/', '', $content);
        $content = preg_replace('/\s*```$/', '', $content);

        $json = json_decode($content, true);

        if ($json === null) {
            return null;
        }

        $flagged = $json['flagged'] ?? false;

        if (!$flagged) {
            return null;
        }

        $category = $json['category'] ?? 'other';

        return match ($category) {
            'sexual' => ModerationReason::SEXUAL_CONTENT,
            'violence' => ModerationReason::VIOLENCE,
            'hate' => ModerationReason::HATE_SPEECH,
            'harassment' => ModerationReason::HARASSMENT,
            default => ModerationReason::INAPPROPRIATE_CONTENT,
        };
    }
}
