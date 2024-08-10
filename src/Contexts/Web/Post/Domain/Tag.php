<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;

class Tag
{
    private Uuid $id;
    private string $name;
}