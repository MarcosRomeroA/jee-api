<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain;

use Doctrine\ORM\Mapping as ORM;
use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Web\User\Domain\CustomTypes\UserId;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'user_id', length: 36)]
    protected UserId $id;

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