<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Persistence\Doctrine\ContainsNullableEmbeddable;
use App\Contexts\Shared\Infrastructure\Persistence\Doctrine\Nullable;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ContainsNullableEmbeddable]
class User extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[Nullable]
    #[Embedded(class: FirstnameValue::class, columnPrefix: false)]
    private ?FirstnameValue $firstname;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $lastname;

    #[ORM\Column(length: 50)]
    private string $username;

    #[ORM\Column(length: 200)]
    private string $email;

    #[ORM\Column(length: 200)]
    private string $password;
}