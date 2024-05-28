<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column]
    protected ?int $id;

    #[ORM\Column(length: 200)]
    private string $firstname;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $lastname;

    #[ORM\Column(length: 50)]
    private string $username;

    #[ORM\Column(length: 200)]
    private string $email;

    #[ORM\Column(length: 200)]
    private string $password;
}