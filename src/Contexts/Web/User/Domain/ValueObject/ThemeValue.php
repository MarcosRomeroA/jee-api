<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\ValueObject;

use App\Contexts\Shared\Domain\ValueObject\StringValueObject;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class ThemeValue extends StringValueObject
{
    public const LIGHT = 'light';
    public const DARK = 'dark';

    private const VALID_THEMES = [self::LIGHT, self::DARK];

    #[ORM\Column(name: 'theme', type: 'string', length: 10, options: ['default' => self::LIGHT])]
    protected string $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        parent::__construct($value);
    }

    private function validate(string $value): void
    {
        if (!in_array($value, self::VALID_THEMES, true)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid theme "%s". Valid themes are: %s', $value, implode(', ', self::VALID_THEMES))
            );
        }
    }

    public static function light(): self
    {
        return new self(self::LIGHT);
    }

    public static function dark(): self
    {
        return new self(self::DARK);
    }

    public static function default(): self
    {
        return self::light();
    }
}
