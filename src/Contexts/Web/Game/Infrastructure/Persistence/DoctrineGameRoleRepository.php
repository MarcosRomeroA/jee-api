<?php declare(strict_types=1);

namespace App\Contexts\Web\Game\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\Game\Domain\GameRole;
use App\Contexts\Web\Game\Domain\GameRoleRepository;
use App\Contexts\Web\Game\Domain\Exception\GameRoleNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineGameRoleRepository extends ServiceEntityRepository implements GameRoleRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameRole::class);
    }

    public function save(GameRole $gameRole): void
    {
        $this->getEntityManager()->persist($gameRole);
        $this->getEntityManager()->flush();
    }

    public function findById(Uuid $id): GameRole
    {
        $gameRole = $this->findOneBy(["id" => $id->value()]);

        if (!$gameRole) {
            throw new GameRoleNotFoundException($id->value());
        }

        return $gameRole;
    }

    public function existsById(Uuid $id): bool
    {
        return $this->count(['id' => $id->value()]) > 0;
    }
}

