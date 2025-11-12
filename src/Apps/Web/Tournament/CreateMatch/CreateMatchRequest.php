<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\CreateMatch;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateMatchRequest extends BaseRequest
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    public mixed $id;

    #[Assert\NotBlank]
    #[Assert\Uuid]
    public mixed $tournamentId;

    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    #[Assert\Positive]
    public mixed $round;

    #[Assert\NotBlank]
    #[Assert\Type('array')]
    #[Assert\Count(min: 2)]
    #[Assert\All([
        new Assert\Uuid()
    ])]
    public mixed $teamIds;

    #[Assert\Type('string')]
    #[Assert\Length(max: 100)]
    public mixed $name;

    #[Assert\Type('string')]
    #[Assert\DateTime(format: 'Y-m-d\TH:i:s\Z')]
    public mixed $scheduledAt;

    public function getScheduledAtAsDateTime(): ?\DateTimeImmutable
    {
        if ($this->scheduledAt === null || $this->scheduledAt === '') {
            return null;
        }

        return new \DateTimeImmutable($this->scheduledAt);
    }
}

