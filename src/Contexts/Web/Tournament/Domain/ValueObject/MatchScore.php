<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final readonly class MatchScore
{
    #[ORM\Column(type: 'integer')]
    private int $score;

    public function __construct(int $score)
    {
        $this->ensureIsValid($score);
        $this->score = $score;
    }

    private function ensureIsValid(int $score): void
    {
        if ($score < 0) {
            throw new \InvalidArgumentException('Score cannot be negative');
        }
    }

    public function value(): int
    {
        return $this->score;
    }

    public function equals(MatchScore $other): bool
    {
        return $this->score === $other->score;
    }

    public function isGreaterThan(MatchScore $other): bool
    {
        return $this->score > $other->score;
    }
}

