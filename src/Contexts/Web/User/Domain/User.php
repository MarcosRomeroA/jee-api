<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use App\Contexts\Web\User\Domain\ValueObject\LastnameValue;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[Embedded(class: FirstnameValue::class, columnPrefix: false)]
    private FirstnameValue $firstname;

    #[Embedded(class: LastnameValue::class, columnPrefix: false)]
    private LastnameValue $lastname;

    #[Embedded(class: UsernameValue::class, columnPrefix: false)]
    private UsernameValue $username;

    #[Embedded(class: EmailValue::class, columnPrefix: false)]
    private EmailValue $email;

    #[Embedded(class: PasswordValue::class, columnPrefix: false)]
    private PasswordValue $password;

    public function __construct(
        Uuid $id,
        FirstnameValue $firstname,
        LastnameValue $lastname,
        UsernameValue $username,
        EmailValue $email,
        PasswordValue $password,
    )
    {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
    }

    public static function create(
        Uuid $id,
        FirstnameValue $firstname,
        LastnameValue $lastname,
        UsernameValue $username,
        EmailValue $email,
        PasswordValue $password,
    ): self
    {
        $user = new self($id, $firstname, $lastname, $username, $email, $password);

        $user->record(new UserCreatedDomainEvent(
            $id
        ));

        return $user;
    }

    public function update(
        FirstnameValue $firstname,
        LastnameValue $lastname,
        UsernameValue $username,
        EmailValue $email,
    ): self {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->username = $username;
        $this->email = $email;

        $this->record(new UserUpdatedDomainEvent(
            $this->id
        ));

        return $this;
    }

    public function updatePassword(
        PasswordValue $password,
    ): self {
        $this->password = $password;

        $this->record(new UserPasswordUpdatedDomainEvent(
            $this->id
        ));

        return $this;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getFirstname(): FirstnameValue
    {
        return $this->firstname;
    }

    public function getLastname(): LastnameValue
    {
        return $this->lastname;
    }

    public function getUsername(): UsernameValue
    {
        return $this->username;
    }

    public function getEmail(): EmailValue
    {
        return $this->email;
    }

    public function getPassword(): PasswordValue
    {
        return $this->password;
    }
}