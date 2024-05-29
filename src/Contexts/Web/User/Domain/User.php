<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Persistence\Doctrine\ContainsNullableEmbeddable;
use App\Contexts\Shared\Infrastructure\Persistence\Doctrine\Nullable;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use App\Contexts\Web\User\Domain\ValueObject\LastnameValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;
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

    #[Embedded(class: FirstnameValue::class, columnPrefix: false)]
    private FirstnameValue $firstname;

    #[Nullable]
    #[Embedded(class: LastnameValue::class, columnPrefix: false)]
    private ?LastnameValue $lastname;

    #[Embedded(class: UsernameValue::class, columnPrefix: false)]
    private UsernameValue $username;

    #[Embedded(class: EmailValue::class, columnPrefix: false)]
    private string $email;

    #[ORM\Column(length: 200)]
    private string $password;
}