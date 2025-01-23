<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use App\Contexts\Web\User\Domain\ValueObject\LastnameValue;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;
use App\Contexts\Web\User\Domain\ValueObject\ProfileImageValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[Embedded(class: ProfileImageValue::class, columnPrefix: false)]
    private ProfileImageValue $profileImage;

    /**
     * @var ArrayCollection<Follow>
     */
    #[ORM\OneToMany(targetEntity: Follow::class, mappedBy: "follower", cascade: ["persist", "remove"])]
    private Collection $following;

    /**
     * @var ArrayCollection<Follow>
     */
    #[ORM\OneToMany(targetEntity: Follow::class, mappedBy: "followed", cascade: ["persist", "remove"])]
    private Collection $followers;

    private ?string $urlProfileImage = null;

    public function __construct(
        Uuid $id,
        FirstnameValue $firstname,
        LastnameValue $lastname,
        UsernameValue $username,
        EmailValue $email,
        PasswordValue $password,
    )
    {
        $this->following = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->profileImage = new ProfileImageValue("");
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

    public function getProfileImage(): ProfileImageValue
    {
        return $this->profileImage;
    }

    public function setProfileImage(ProfileImageValue $profileImage): void
    {
        $this->profileImage = $profileImage;
    }

    public function getFollowings(): Collection
    {
        return $this->following;
    }

    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function follow(Follow $follow): void
    {
        foreach ($this->following as $following) {
            if ($following->getFollowed() === $follow->getFollowed()) {
                return;
            }
        }

        $this->following->add($follow);
    }

    public function getFollowedRelation(User $user): ?Follow
    {
        foreach ($this->following as $following) {
            if ($following->getFollowed() === $user) {
                return $following;
            }
        }
        return null;
    }

    public function getUrlProfileImage(): ?string
    {
        return $this->urlProfileImage;
    }

    public function setUrlProfileImage(?string $urlProfileImage): void
    {
        $this->urlProfileImage = $urlProfileImage;
    }
}