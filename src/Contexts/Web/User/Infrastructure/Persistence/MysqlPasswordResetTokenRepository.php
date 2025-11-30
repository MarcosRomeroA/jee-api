<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Infrastructure\Persistence;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\PasswordResetToken;
use App\Contexts\Web\User\Domain\PasswordResetTokenRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class MysqlPasswordResetTokenRepository extends ServiceEntityRepository implements PasswordResetTokenRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordResetToken::class);
    }

    public function save(PasswordResetToken $token): void
    {
        $this->getEntityManager()->persist($token);
        $this->getEntityManager()->flush();
    }

    public function findByTokenHash(string $tokenHash): ?PasswordResetToken
    {
        return $this->createQueryBuilder('prt')
            ->where('prt.token.tokenHash = :tokenHash')
            ->setParameter('tokenHash', $tokenHash)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findValidByUserId(Uuid $userId): ?PasswordResetToken
    {
        return $this->createQueryBuilder('prt')
            ->join('prt.user', 'u')
            ->where('u.id = :userId')
            ->andWhere('prt.expiresAt > :now')
            ->setParameter('userId', $userId->value())
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('prt.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function deleteByUserId(Uuid $userId): void
    {
        $this->createQueryBuilder('prt')
            ->delete()
            ->where('prt.user = :userId')
            ->setParameter('userId', $userId->value())
            ->getQuery()
            ->execute();
    }

    public function deleteExpired(): void
    {
        $this->createQueryBuilder('prt')
            ->delete()
            ->where('prt.expiresAt < :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->execute();
    }

    public function delete(PasswordResetToken $token): void
    {
        $this->getEntityManager()->remove($token);
        $this->getEntityManager()->flush();
    }
}
