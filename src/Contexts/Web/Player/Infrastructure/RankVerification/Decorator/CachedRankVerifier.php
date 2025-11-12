<?php declare(strict_types=1);

namespace App\Contexts\Web\Player\Infrastructure\RankVerification\Decorator;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Player\Domain\Player;
use App\Contexts\Web\Player\Domain\Service\RankVerifier;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Decorador que cachea las verificaciones de rank para reducir llamadas a APIs externas
 * Útil para evitar rate limiting y mejorar performance
 */
final class CachedRankVerifier implements RankVerifier
{
    private const CACHE_TTL = 3600; // 1 hora

    public function __construct(
        private readonly RankVerifier $inner,
        private readonly CacheItemPoolInterface $cache
    ) {
    }

    public function verify(Player $player, Uuid $gameId): bool
    {
        $cacheKey = $this->getCacheKey('verify', $player->username(), $gameId->value());

        $cacheItem = $this->cache->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $result = $this->inner->verify($player, $gameId);

        $cacheItem->set($result);
        $cacheItem->expiresAfter(self::CACHE_TTL);
        $this->cache->save($cacheItem);

        return $result;
    }

    public function getRankInfo(string $username, string $gameIdentifier): array
    {
        $cacheKey = $this->getCacheKey('rank_info', $username, $gameIdentifier);

        $cacheItem = $this->cache->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $result = $this->inner->getRankInfo($username, $gameIdentifier);

        $cacheItem->set($result);
        $cacheItem->expiresAfter(self::CACHE_TTL);
        $this->cache->save($cacheItem);

        return $result;
    }

    /**
     * Genera una clave de caché única
     */
    private function getCacheKey(string $operation, string $username, string $identifier): string
    {
        return sprintf(
            'rank_verifier.%s.%s.%s',
            $operation,
            md5($username),
            md5($identifier)
        );
    }

    /**
     * Limpia la caché para un jugador específico
     */
    public function clearCache(string $username, string $gameIdentifier): void
    {
        $verifyKey = $this->getCacheKey('verify', $username, $gameIdentifier);
        $rankInfoKey = $this->getCacheKey('rank_info', $username, $gameIdentifier);

        $this->cache->deleteItems([$verifyKey, $rankInfoKey]);
    }
}

