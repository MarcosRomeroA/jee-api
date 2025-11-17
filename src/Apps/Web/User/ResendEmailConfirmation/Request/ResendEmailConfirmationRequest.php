<?php

declare(strict_types=1);

namespace App\Apps\Web\User\ResendEmailConfirmation\Request;

use App\Contexts\Web\User\Application\ResendEmailConfirmation\ResendEmailConfirmationCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ResendEmailConfirmationRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $userId
    ) {
    }

    public static function fromHttp(Request $request): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            $data['userId'] ?? ''
        );
    }

    public function toCommand(): ResendEmailConfirmationCommand
    {
        return new ResendEmailConfirmationCommand($this->userId);
    }
}
