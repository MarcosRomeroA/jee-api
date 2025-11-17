<?php

declare(strict_types=1);

namespace App\Apps\Web\User\SocialNetwork\Request;

use App\Contexts\Web\User\Application\AddUserSocialNetwork\AddUserSocialNetworkCommand;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class AddUserSocialNetworkRequest
{
    public function __construct(
        public string $userId,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $socialNetworkId,
        #[Assert\NotBlank]
        #[Assert\Type("string")]
        #[Assert\Length(min: 1, max: 255)]
        public string $username
    ) {
    }

    public static function fromHttp(string $socialNetworkId, string $username, string $userId): self
    {
        return new self(
            $userId,
            $socialNetworkId,
            $username
        );
    }

    public function toCommand(): AddUserSocialNetworkCommand
    {
        return new AddUserSocialNetworkCommand(
            $this->userId,
            $this->socialNetworkId,
            $this->username
        );
    }
}
