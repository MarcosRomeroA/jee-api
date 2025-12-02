<?php

declare(strict_types=1);

namespace App\Contexts\Web\Player\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class GameAccountDataValue
{
    #[ORM\Column(name: 'account_data', type: 'json', nullable: true)]
    private ?array $value;

    public function __construct(?array $value)
    {
        // Normalize steamId if it's a URL
        if (isset($value['steamId']) && is_string($value['steamId'])) {
            $value['steamId'] = $this->extractSteamId($value['steamId']);
        }

        $this->value = $value;
    }

    public function value(): ?array
    {
        return $this->value;
    }

    /**
     * Region for Riot games (e.g., "las", "na", "euw")
     */
    public function region(): ?string
    {
        return $this->value['region'] ?? null;
    }

    /**
     * Username/GameName for Riot games (e.g., "Geosmina")
     */
    public function username(): ?string
    {
        return $this->value['username'] ?? null;
    }

    /**
     * Tag for Riot games (e.g., "JINX")
     */
    public function tag(): ?string
    {
        return $this->value['tag'] ?? null;
    }

    /**
     * Steam ID for Steam games (e.g., "76561198012345678")
     * Supports both direct ID and profile URL extraction
     */
    public function steamId(): ?string
    {
        return $this->value['steamId'] ?? null;
    }

    /**
     * Returns the Riot ID in format "username#tag"
     */
    public function riotId(): ?string
    {
        $username = $this->username();
        $tag = $this->tag();

        if ($username === null || $tag === null) {
            return null;
        }

        return $username . '#' . $tag;
    }

    /**
     * Extracts Steam ID from various formats:
     * - Direct Steam64 ID: "76561198012345678"
     * - Profile URL: "https://steamcommunity.com/profiles/76561198012345678"
     * - Custom URL: "https://steamcommunity.com/id/customname" (returns as-is, needs API resolution)
     */
    private function extractSteamId(string $input): string
    {
        $input = trim($input);

        // If it's already a numeric Steam64 ID
        if (preg_match('/^\d{17}$/', $input)) {
            return $input;
        }

        // Extract from profile URL: steamcommunity.com/profiles/76561198012345678
        if (preg_match('#steamcommunity\.com/profiles/(\d{17})#', $input, $matches)) {
            return $matches[1];
        }

        // Extract custom ID from URL: steamcommunity.com/id/customname
        if (preg_match('#steamcommunity\.com/id/([^/]+)#', $input, $matches)) {
            return $matches[1]; // Return custom ID, will need API resolution later
        }

        // Return as-is if no pattern matches
        return $input;
    }

    public function equals(GameAccountDataValue $other): bool
    {
        return $this->value === $other->value();
    }
}
