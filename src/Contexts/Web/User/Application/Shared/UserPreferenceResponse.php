<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Shared;

use App\Contexts\Shared\Domain\CQRS\Query\Response;
use App\Contexts\Web\User\Domain\UserPreference;
use App\Contexts\Web\User\Domain\ValueObject\LangValue;
use App\Contexts\Web\User\Domain\ValueObject\ThemeValue;

final class UserPreferenceResponse extends Response
{
    public function __construct(
        public readonly string $theme,
        public readonly string $lang,
    ) {
    }

    public static function fromEntity(UserPreference $preference): self
    {
        return new self(
            $preference->getTheme()->value(),
            $preference->getLang()->value(),
        );
    }

    public static function default(): self
    {
        return new self(
            ThemeValue::LIGHT,
            LangValue::SPANISH,
        );
    }

    public function toArray(): array
    {
        return [
            'theme' => $this->theme,
            'lang' => $this->lang,
        ];
    }
}
