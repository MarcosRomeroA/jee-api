<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\DeclineRequest;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\Exception\TournamentRequestNotFoundException;
use App\Contexts\Web\Tournament\Domain\Exception\UnauthorizedException;
use App\Contexts\Web\Tournament\Domain\TournamentRequestRepository;

final readonly class TournamentRequestDecliner
{
    public function __construct(
        private TournamentRequestRepository $requestRepository,
    ) {
    }

    public function __invoke(Uuid $requestId, Uuid $declinedByUserId): void
    {
        $request = $this->requestRepository->findById($requestId);

        if ($request === null || !$request->isPending()) {
            throw new TournamentRequestNotFoundException($requestId->value());
        }

        $tournament = $request->getTournament();

        if ($tournament->getResponsible()->getId()->value() !== $declinedByUserId->value()) {
            throw new UnauthorizedException(
                'Solo el responsable del torneo puede rechazar solicitudes'
            );
        }

        $request->reject();
        $this->requestRepository->save($request);
    }
}
