<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\Find;

use App\Contexts\Web\Tournament\Application\Find\FindTournamentQuery;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class FindTournamentRequest
{
    public function __construct(
        #[Assert\NotBlank]
        public string $id,
    ) {}

    public static function fromId(string $id): self
    {
        return new self($id);
    }

    public function toQuery(): FindTournamentQuery
    {
        return new FindTournamentQuery($this->id);
    }
}

