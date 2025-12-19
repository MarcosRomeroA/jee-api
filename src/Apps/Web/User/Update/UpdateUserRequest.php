<?php

declare(strict_types=1);

namespace App\Apps\Web\User\Update;

use App\Contexts\Web\User\Application\Update\UpdateUserCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateUserRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $id,
        #[Assert\Type('string')]
        public ?string $firstname,
        #[Assert\Type('string')]
        public ?string $lastname,
        #[Assert\Type('string')]
        public ?string $username,
        #[Assert\Email]
        public ?string $email,
    ) {
    }

    public static function fromHttp(Request $request, string $id): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            $id,
            $data['firstname'] ?? null,
            $data['lastname'] ?? null,
            $data['username'] ?? null,
            $data['email'] ?? null,
        );
    }

    public function toCommand(): UpdateUserCommand
    {
        return new UpdateUserCommand(
            $this->id,
            $this->firstname,
            $this->lastname,
            $this->username,
            $this->email,
        );
    }
}
