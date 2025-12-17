<?php

declare(strict_types=1);

namespace App\Contexts\Web\Tournament\Application\FinalizeActiveTournaments;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Tournament\Domain\TournamentRepository;
use App\Contexts\Web\Tournament\Domain\TournamentStatus;
use App\Contexts\Web\Tournament\Domain\TournamentStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final readonly class ActiveTournamentsFinalizer
{
    public function __construct(
        private TournamentRepository $tournamentRepository,
        private TournamentStatusRepository $tournamentStatusRepository,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(): int
    {
        $tournaments = $this->tournamentRepository->findActiveTournamentsToFinalize();

        if (empty($tournaments)) {
            $this->logger->info('[FinalizeActiveTournaments] No active tournaments to finalize');
            return 0;
        }

        $finalizedStatus = $this->tournamentStatusRepository->findById(
            new Uuid(TournamentStatus::FINALIZED)
        );

        if ($finalizedStatus === null) {
            $this->logger->error('[FinalizeActiveTournaments] Finalized status not found');
            return 0;
        }

        $count = 0;
        foreach ($tournaments as $tournament) {
            try {
                // Change status to finalized without setting positions
                // Positions can be set manually later by the tournament responsible
                $tournament->finalize($finalizedStatus);

                $this->entityManager->flush();

                $this->logger->info(
                    '[FinalizeActiveTournaments] Tournament finalized',
                    [
                        'tournament_id' => $tournament->getId()->value(),
                        'tournament_name' => $tournament->getName(),
                        'updated_at' => $tournament->getUpdatedAt()->format('Y-m-d H:i:s'),
                    ]
                );

                $count++;
            } catch (\Exception $e) {
                $this->logger->error(
                    '[FinalizeActiveTournaments] Error finalizing tournament',
                    [
                        'tournament_id' => $tournament->getId()->value(),
                        'error' => $e->getMessage(),
                    ]
                );
            }
        }

        $this->logger->info(
            "[FinalizeActiveTournaments] Finalized $count tournaments"
        );

        return $count;
    }
}
