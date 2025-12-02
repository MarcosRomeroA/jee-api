<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\FindBackgroundImage;

use App\Contexts\Shared\Domain\CQRS\Query\Response;

final class BackgroundImageResponse extends Response
{
    public function __construct(
        public readonly ?string $backgroundImage,
    ) {
    }

    public function toArray(): array
    {
        return [
            'backgroundImage' => $this->backgroundImage,
        ];
    }
}
