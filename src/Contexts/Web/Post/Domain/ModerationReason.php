<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain;

enum ModerationReason: string
{
    case INAPPROPRIATE_CONTENT = 'inappropriate_content';
    case SPAM = 'spam';
    case HARASSMENT = 'harassment';
    case HATE_SPEECH = 'hate_speech';
    case VIOLENCE = 'violence';
    case SEXUAL_CONTENT = 'sexual_content';
    case MISINFORMATION = 'misinformation';
    case COPYRIGHT_VIOLATION = 'copyright_violation';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::INAPPROPRIATE_CONTENT => 'Contenido inapropiado',
            self::SPAM => 'Spam',
            self::HARASSMENT => 'Acoso',
            self::HATE_SPEECH => 'Discurso de odio',
            self::VIOLENCE => 'Violencia',
            self::SEXUAL_CONTENT => 'Contenido sexual',
            self::MISINFORMATION => 'Desinformación',
            self::COPYRIGHT_VIOLATION => 'Violación de derechos de autor',
            self::OTHER => 'Otro',
        };
    }

    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
