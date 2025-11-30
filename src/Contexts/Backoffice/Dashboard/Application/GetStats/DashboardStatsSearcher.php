<?php

declare(strict_types=1);

namespace App\Contexts\Backoffice\Dashboard\Application\GetStats;

use Doctrine\ORM\EntityManagerInterface;

final readonly class DashboardStatsSearcher
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(): DashboardStatsResponse
    {
        $connection = $this->entityManager->getConnection();

        $usersCount = (int) $connection->fetchOne('SELECT COUNT(*) FROM `user`');
        $postsCount = (int) $connection->fetchOne('SELECT COUNT(*) FROM post WHERE deleted_at IS NULL');
        $teamsCount = (int) $connection->fetchOne('SELECT COUNT(*) FROM team WHERE deleted_at IS NULL');
        $tournamentsCount = (int) $connection->fetchOne('SELECT COUNT(*) FROM tournament WHERE deleted_at IS NULL');

        return new DashboardStatsResponse(
            usersCount: $usersCount,
            postsCount: $postsCount,
            teamsCount: $teamsCount,
            tournamentsCount: $tournamentsCount,
        );
    }
}
