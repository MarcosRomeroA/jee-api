<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\Create;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateTournamentRequest extends BaseRequest
{
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $id;

    #[Assert\NotNull, Assert\Type("string")]
    public mixed $gameId;

    #[Assert\NotNull, Assert\Type("string")]
    public mixed $responsibleId;

    #[Assert\NotNull, Assert\Type("string")]
    public mixed $name;

    #[Assert\Type("string")]
    public mixed $description;

    #[Assert\NotNull, Assert\Type("int"), Assert\GreaterThan(0)]
    public mixed $maxTeams;

    #[Assert\NotNull, Assert\Type("bool")]
    public mixed $isOfficial;

    #[Assert\Type("string")]
    public mixed $image;

    #[Assert\Type("string")]
    public mixed $prize;

    #[Assert\Type("string")]
    public mixed $region;

    #[Assert\NotNull, Assert\Type("string")]
    public mixed $startAt;

    #[Assert\NotNull, Assert\Type("string")]
    public mixed $endAt;

    #[Assert\Type("string")]
    public mixed $minGameRankId;

    #[Assert\Type("string")]
    public mixed $maxGameRankId;
}

