<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\AddTeam;

use App\Contexts\Shared\Infrastructure\Symfony\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class TournamentAddTeamRequest extends BaseRequest
{
    #[Assert\NotNull, Assert\Type("string")]
    public mixed $addedByUserId;
}

