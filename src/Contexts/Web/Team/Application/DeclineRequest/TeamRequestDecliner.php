<?php

declare(strict_types=1);

namespace App\Contexts\Web\Team\Application\DeclineRequest;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Team\Domain\Exception\RequestNotFoundException;
use App\Contexts\Web\Team\Domain\Exception\UnauthorizedException;
use App\Contexts\Web\Team\Domain\TeamRequestRepository;

final readonly class TeamRequestDecliner
{
    public function __construct(
        private TeamRequestRepository $teamRequestRepository,
    ) {
    }

    public function __invoke(Uuid $requestId, Uuid $declinedByUserId): void
    {
        $request = $this->teamRequestRepository->findById($requestId);

        if ($request === null) {
            throw new RequestNotFoundException($requestId->value());
        }

        if ($request->getTeam()->getCreator() === null ||
            $request->getTeam()->getCreator()->getId()->value() !== $declinedByUserId->value()) {
            throw new UnauthorizedException('Solo el creador del equipo puede rechazar solicitudes');
        }

        if ($request->getStatus() !== 'pending') {
            throw new UnauthorizedException('La solicitud ya fue procesada');
        }

        $request->reject();
        $this->teamRequestRepository->save($request);
    }
}
