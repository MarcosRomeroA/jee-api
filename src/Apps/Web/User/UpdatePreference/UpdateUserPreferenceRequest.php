<?php declare(strict_types=1);

namespace App\Apps\Web\User\UpdatePreference;

use App\Contexts\Web\User\Application\UpdatePreference\UpdateUserPreferenceCommand;
use App\Contexts\Web\User\Domain\ValueObject\LangValue;
use App\Contexts\Web\User\Domain\ValueObject\ThemeValue;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateUserPreferenceRequest
{
    public function __construct(
        public string $userId,

        #[Assert\NotBlank]
        #[Assert\Choice(choices: [ThemeValue::LIGHT, ThemeValue::DARK], message: 'Invalid theme. Valid values: light, dark')]
        public string $theme,

        #[Assert\NotBlank]
        #[Assert\Choice(choices: [LangValue::SPANISH, LangValue::ENGLISH, LangValue::PORTUGUESE], message: 'Invalid language. Valid values: es, en, pt')]
        public string $lang,
    ) {
    }

    public static function fromHttp(Request $request, string $sessionId): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            $sessionId,
            $data['theme'] ?? '',
            $data['lang'] ?? '',
        );
    }

    public function toCommand(): UpdateUserPreferenceCommand
    {
        return new UpdateUserPreferenceCommand(
            $this->userId,
            $this->theme,
            $this->lang,
        );
    }
}
