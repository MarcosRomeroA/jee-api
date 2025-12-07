<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\UpdatePreference;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\ValueObject\LangValue;
use App\Contexts\Web\User\Domain\ValueObject\ThemeValue;

final readonly class UpdateUserPreferenceCommandHandler implements CommandHandler
{
    public function __construct(
        private UserPreferenceUpdater $updater,
    ) {
    }

    public function __invoke(UpdateUserPreferenceCommand $command): void
    {
        $userId = new Uuid($command->userId);
        $theme = new ThemeValue($command->theme);
        $lang = new LangValue($command->lang);

        $this->updater->__invoke($userId, $theme, $lang);
    }
}
