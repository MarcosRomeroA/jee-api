<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Moderation;

use App\Contexts\Shared\Domain\Moderation\ModerationReason;

interface TextModerationService
{
    /**
     * Analyzes text content for moderation.
     * Returns null if content is safe, or ModerationReason if flagged.
     */
    public function moderate(string $text): ?ModerationReason;
}
