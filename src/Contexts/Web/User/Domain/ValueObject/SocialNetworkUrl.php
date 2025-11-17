<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class SocialNetworkUrl
{
    #[ORM\Column(type: 'string', length: 255)]
    private string $url;

    public function __construct(string $url)
    {
        $this->ensureIsValid($url);
        $this->url = $url;
    }

    private function ensureIsValid(string $url): void
    {
        if (empty($url)) {
            throw new \InvalidArgumentException('Social network URL cannot be empty');
        }

        if (strlen($url) > 255) {
            throw new \InvalidArgumentException('Social network URL cannot exceed 255 characters');
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Social network URL must be a valid URL');
        }
    }

    public function value(): string
    {
        return $this->url;
    }

    public function __toString(): string
    {
        return $this->url;
    }
}
