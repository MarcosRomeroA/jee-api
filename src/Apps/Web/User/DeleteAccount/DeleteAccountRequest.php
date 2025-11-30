<?php

declare(strict_types=1);

namespace App\Apps\Web\User\DeleteAccount;

use App\Contexts\Web\User\Application\DeleteAccount\DeleteAccountCommand;

final readonly class DeleteAccountRequest
{
    public function __construct(
        public string $userId,
    ) {
    }

    public static function fromHttp(string $id): self
    {
        return new self($id);
    }

    public function toCommand(): DeleteAccountCommand
    {
        return new DeleteAccountCommand($this->userId);
    }
}
