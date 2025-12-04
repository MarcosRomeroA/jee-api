<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Event\Create;

use App\Contexts\Web\Event\Application\Create\CreateEventCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateEventRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $id,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(max: 255)]
        public string $name,
        #[Assert\Type('string')]
        public ?string $description,
        #[Assert\Uuid]
        public ?string $gameId,
        #[Assert\Type('string')]
        public ?string $image,
        #[Assert\NotBlank]
        #[Assert\Choice(choices: ['presencial', 'virtual'])]
        public string $type,
        #[Assert\NotBlank]
        #[Assert\DateTime(format: \DateTimeInterface::ATOM)]
        public string $startAt,
        #[Assert\NotBlank]
        #[Assert\DateTime(format: \DateTimeInterface::ATOM)]
        public string $endAt,
    ) {
    }

    public static function fromHttp(Request $request, string $id): self
    {
        $data = json_decode($request->getContent(), true) ?? [];

        return new self(
            $id,
            $data['name'] ?? '',
            $data['description'] ?? null,
            $data['gameId'] ?? null,
            $data['image'] ?? null,
            $data['type'] ?? '',
            $data['startAt'] ?? '',
            $data['endAt'] ?? '',
        );
    }

    public function toCommand(): CreateEventCommand
    {
        return new CreateEventCommand(
            $this->id,
            $this->name,
            $this->description,
            $this->gameId,
            $this->image,
            $this->type,
            $this->startAt,
            $this->endAt,
        );
    }
}
