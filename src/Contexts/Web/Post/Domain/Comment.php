<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\Moderation\ModerationReason;
use App\Contexts\Shared\Domain\Traits\Timestamps;
use App\Contexts\Shared\Domain\ValueObject\CreatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\UpdatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Persistence\Doctrine\ContainsNullableEmbeddable;
use App\Contexts\Web\Post\Domain\Events\CommentModeratedDomainEvent;
use App\Contexts\Web\Post\Domain\ValueObject\CommentValue;
use App\Contexts\Web\User\Domain\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;

#[ContainsNullableEmbeddable]
#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name: "post_comment")]
#[ORM\Index(name: "IDX_COMMENT_DISABLED", columns: ["disabled"])]
class Comment extends AggregateRoot
{
    use Timestamps;
    #[ORM\Id]
    #[ORM\Column(type: "uuid", length: 36)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: "comments")]
    private Post $post;

    #[Embedded(class: CommentValue::class, columnPrefix: false)]
    private CommentValue $comment;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $disabled = false;

    #[ORM\Column(type: "string", nullable: true, enumType: ModerationReason::class)]
    private ?ModerationReason $moderationReason = null;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?\DateTimeImmutable $disabledAt = null;

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

    public function getPost(): Post
    {
        return $this->post;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function getModerationReason(): ?ModerationReason
    {
        return $this->moderationReason;
    }

    public function getDisabledAt(): ?\DateTimeImmutable
    {
        return $this->disabledAt;
    }

    public function disable(ModerationReason $reason): void
    {
        $this->disabled = true;
        $this->moderationReason = $reason;
        $this->disabledAt = new \DateTimeImmutable();
    }

    public function enable(): void
    {
        $this->disabled = false;
        $this->moderationReason = null;
        $this->disabledAt = null;
    }
}
