<?php

declare(strict_types=1);

namespace App\Apps\Backoffice\Admin\Create;

use App\Contexts\Backoffice\Admin\Application\Create\CreateAdminCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateAdminRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $id,
        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 100)]
        public string $name,
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 50)]
        #[Assert\Regex(pattern: '/^[a-zA-Z0-9_.-]+$/', message: 'Admin user can only contain letters, numbers, dots, hyphens and underscores')]
        public string $user,
        #[Assert\NotBlank]
        #[Assert\Length(min: 8)]
        public string $password,
    ) {
    }

    public static function fromHttp(Request $request, string $id): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            $id,
            $data['name'] ?? '',
            $data['user'] ?? '',
            $data['password'] ?? '',
        );
    }

    public function toCommand(): CreateAdminCommand
    {
        return new CreateAdminCommand(
            $this->id,
            $this->name,
            $this->user,
            $this->password,
        );
    }
}
