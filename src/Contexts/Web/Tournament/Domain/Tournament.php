<?php declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Web\Game\Domain\Game;

class Tournament extends AggregateRoot
{
    private string $name;

    private bool $isOfficial;

    private Game $game;

    private $players;
}