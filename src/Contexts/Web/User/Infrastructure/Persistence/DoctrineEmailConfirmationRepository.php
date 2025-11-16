<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\EmailConfirmation;
use App\Contexts\Web\User\Domain\EmailConfirmationRepository;
use App\Contexts\Web\User\Domain\ValueObject\EmailConfirmationToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineEmailConfirmationRepository extends ServiceEntityRepository implements EmailConfirmationRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailConfirmation::class);
    }

    public function save(EmailConfirmation $emailConfirmation): void
    {
        $this->getEntityManager()->persist($emailConfirmation);
        $this->getEntityManager()->flush();
    }

    public function findById(Uuid $id): ?EmailConfirmation
    {
        return $this->findOneBy(["id" => $id->value()]);
    }

    public function findByToken(
        EmailConfirmationToken $token,
    ): ?EmailConfirmation {
        return $this->createQueryBuilder('ec')
            ->where('ec.token.token = :token')
            ->setParameter('token', $token->value())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByUserId(Uuid $userId): ?EmailConfirmation
    {
        return $this->createQueryBuilder("ec")
            ->where("ec.user = :userId")
            ->setParameter("userId", $userId)
            ->orderBy("ec.createdAt", "DESC")
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function delete(EmailConfirmation $emailConfirmation): void
    {
        $this->getEntityManager()->remove($emailConfirmation);
        $this->getEntityManager()->flush();
    }
}
