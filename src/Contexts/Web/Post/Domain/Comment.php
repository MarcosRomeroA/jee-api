<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\Traits\Timestamps;
use App\Contexts\Shared\Domain\ValueObject\CreatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\UpdatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Persistence\Doctrine\ContainsNullableEmbeddable;

use App\Contexts\Web\Post\Domain\ValueObject\CommentValue;
use App\Contexts\Web\User\Domain\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;

#[ContainsNullableEmbeddable]
#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name: "post_comment")]
class Comment extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: "comments")]
    private Post $post;

    #[Embedded(class: CommentValue::class, columnPrefix: false)]
    private CommentValue $comment;

    use Timestamps;

    public function __construct(Uuid $id, CommentValue $comment)
    {
        $this->id = $id;
        $this->comment = $comment;
        $this->createdAt = new CreatedAtValue();
        $this->updatedAt = new UpdatedAtValue($this->createdAt->value());
    }

    public static function create(
        Uuid $id,
        CommentValue $comment,
        User $user,
        Post $post,
    ): self {
        $commentEntity = new self($id, $comment);
        $commentEntity->user = $user;
        $commentEntity->post = $post;

        return $commentEntity;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getComment(): CommentValue
    {
        return $this->comment;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setPost(Post $post): void
    {
        $this->post = $post;
    }
}
