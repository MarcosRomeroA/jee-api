<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\UpdatePreference;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class UpdateUserPreferenceCommand implements Command
{
    public function __construct(
        public string $userId,
        public string $theme,
        public string $lang,
    ) {
    }
}
