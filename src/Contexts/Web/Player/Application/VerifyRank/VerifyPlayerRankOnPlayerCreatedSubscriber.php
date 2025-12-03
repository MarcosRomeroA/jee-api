<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Application\VerifyRank;

use App\Contexts\Shared\Domain\CQRS\Event\DomainEventSubscriber;
use App\Contexts\Web\Player\Domain\Events\PlayerCreatedDomainEvent;
use App\Contexts\Web\Player\Domain\Exception\RankVerificationException;
use Psr\Log\LoggerInterface;

final readonly class VerifyPlayerRankOnPlayerCreatedSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private PlayerRankVerifier $playerRankVerifier,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(PlayerCreatedDomainEvent $event): void
    {
        $playerId = $event->getAggregateId();

        try {
            $this->playerRankVerifier->__invoke($playerId);
            $this->logger->info("Player rank verified successfully", [
                'playerId' => $playerId->value(),
            ]);
        } catch (RankVerificationException $e) {
            // Log but don't fail - rank verification is not critical
            $this->logger->warning("Failed to verify player rank", [
                'playerId' => $playerId->value(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    public static function subscribedTo(): array
    {
        return [PlayerCreatedDomainEvent::class];
    }
}
