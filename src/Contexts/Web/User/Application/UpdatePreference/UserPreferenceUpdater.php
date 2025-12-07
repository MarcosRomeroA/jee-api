<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\UpdatePreference;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\UserPreference;
use App\Contexts\Web\User\Domain\UserPreferenceRepository;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\LangValue;
use App\Contexts\Web\User\Domain\ValueObject\ThemeValue;

final readonly class UserPreferenceUpdater
{
    public function __construct(
        private UserPreferenceRepository $preferenceRepository,
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(
        Uuid $userId,
        ThemeValue $theme,
        LangValue $lang,
    ): void {
        $user = $this->userRepository->findById($userId);
        $preference = $this->preferenceRepository->findByUser($user);

        if ($preference === null) {
            $preference = UserPreference::create(
                Uuid::random(),
                $user,
                $theme,
                $lang,
            );
        } else {
            $preference->update($theme, $lang);
        }

        $this->preferenceRepository->save($preference);
    }
}
