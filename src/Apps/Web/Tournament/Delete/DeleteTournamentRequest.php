<?php declare(strict_types=1);

namespace App\Apps\Web\Tournament\Delete;

use App\Contexts\Web\Tournament\Application\Delete\DeleteTournamentCommand;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class DeleteTournamentRequest
{
    public function __construct(
        #[Assert\NotBlank]
        public string $id,
    ) {}

    public static function fromId(string $id): self
    {
        return new self($id);
    }

    public function toCommand(): DeleteTournamentCommand
    {
        return new DeleteTournamentCommand($this->id);
    }
}

