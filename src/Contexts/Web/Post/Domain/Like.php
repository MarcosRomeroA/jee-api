<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\Traits\Timestamps;
use App\Contexts\Shared\Domain\ValueObject\CreatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\UpdatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Persistence\Doctrine\ContainsNullableEmbeddable;

use App\Contexts\Web\User\Domain\User;
use Doctrine\ORM\Mapping as ORM;

#[ContainsNullableEmbeddable]
#[ORM\Entity(repositoryClass: LikeRepository::class)]
#[ORM\Table(name: "post_like")]
class Like extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: "comments")]
    private Post $post;

    use Timestamps;

    public function __construct(Uuid $id, User $user)
    {
        $this->id = $id;
        $this->user = $user;
        $this->createdAt = new CreatedAtValue();
        $this->updatedAt = new UpdatedAtValue($this->createdAt->value());
    }

    public static function create(Uuid $id, User $user, Post $post): self
    {
        $like = new self($id, $user);
        $like->post = $post;

        return $like;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setPost(Post $post): void
    {
        $this->post = $post;
    }
}
