<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain\Moderation;

use App\Contexts\Shared\Domain\Moderation\ModerationReason;

interface ImageModerationService
{
    /**
     * Analyzes image content for moderation.
     * Returns null if content is safe, or ModerationReason if flagged.
     */
    public function moderate(string $imageUrl): ?ModerationReason;
}
