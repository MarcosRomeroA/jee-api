<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;
use App\Contexts\Web\User\Domain\User;
use Doctrine\Common\Collections\Collection;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use App\Contexts\Shared\Domain\Traits\Timestamps;
use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Web\Post\Domain\ValueObject\BodyValue;
use App\Contexts\Shared\Domain\ValueObject\CreatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\UpdatedAtValue;
use App\Contexts\Web\Post\Domain\Events\PostLikedDomainEvent;
use App\Contexts\Web\Post\Domain\Events\PostCreatedDomainEvent;
use App\Contexts\Web\Post\Domain\Events\PostCommentedDomainEvent;
use App\Contexts\Shared\Infrastructure\Persistence\Doctrine\ContainsNullableEmbeddable;
use App\Contexts\Shared\Domain\Moderation\ModerationReason;

#[ContainsNullableEmbeddable]
#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post extends AggregateRoot
{
    use Timestamps;
    #[ORM\Id]
    #[ORM\Column(type: "uuid", length: 36)]
    private Uuid $id;

    #[Embedded(class: BodyValue::class, columnPrefix: false)]
    private BodyValue $body;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    #[ORM\Column(type: "uuid", length: 36, nullable: true)]
    private ?Uuid $sharedPostId = null;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: "post", cascade: ["persist", "remove"])]
    private ?Collection $comments;

    #[
        ORM\OneToMany(
            targetEntity: Like::class,
            mappedBy: "post",
            cascade: ["persist", "remove"],
            orphanRemoval: true,
        ),
    ]
    private ?Collection $likes;

    #[
        ORM\OneToMany(
            targetEntity: PostResource::class,
            mappedBy: "post",
            cascade: ["persist", "remove"],
        ),
    ]
    private ?Collection $resources;

    #[
        ORM\ManyToMany(targetEntity: Hashtag::class, inversedBy: 'posts'),
        ORM\JoinTable(name: 'post_hashtag'),
    ]
    private Collection $hashtags;

    #[ORM\OneToMany(targetEntity: PostMention::class, mappedBy: 'post', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $mentions;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $disabled = false;

    #[ORM\Column(type: "string", nullable: true, enumType: ModerationReason::class)]
    private ?ModerationReason $moderationReason = null;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?\DateTimeImmutable $disabledAt = null;

    private array $resourceUrls;

    private ?int $sharesQuantity = null;

    private ?Post $sharedPost = null;

    private bool $hasShared = false;

    public function __construct(
        Uuid $id,
        BodyValue $body,
        User $user,
        ?Uuid $sharedPostId,
    ) {
        $this->id = $id;
        $this->body = $body;
        $this->user = $user;
        $this->sharedPostId = $sharedPostId;
        $this->createdAt = new CreatedAtValue();
        $this->updatedAt = new UpdatedAtValue($this->createdAt->value());
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->resources = new ArrayCollection();
        $this->hashtags = new ArrayCollection();
        $this->mentions = new ArrayCollection();
        $this->resourceUrls = [];
    }

    public static function create(
        Uuid $id,
        BodyValue $body,
        User $user,
        array $resources,
        ?Uuid $sharedPostId,
    ): self {
        $post = new self($id, $body, $user, $sharedPostId);

        $post->record(new PostCreatedDomainEvent($id, $resources));

        return $post;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getBody(): BodyValue
    {
        return $this->body;
    }

    public function getCreatedAt(): CreatedAtValue
    {
        return $this->createdAt;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
            $this->record(
                new PostCommentedDomainEvent(
                    $this->id,
                    $comment->getId(),
                    $comment->getUser()->getId(),
                ),
            );
        }

        return $this;
    }

    public function addLike(Like $like): self
    {
        foreach ($this->likes as $l) {
            if (
                $l->getUser()->getId()->value() ===
                $like->getUser()->getId()->value()
            ) {
                return $this;
            }
        }

        $this->likes[] = $like;
        $like->setPost($this);
        $this->record(
            new PostLikedDomainEvent(
                $this->id,
                $like->getId(),
                $like->getUser()->getId(),
            ),
        );

        return $this;
    }

    public function removeLike(User $user): void
    {
        foreach ($this->likes as $like) {
            if ($like->getUser() === $user) {
                $this->likes->removeElement($like);
            }
        }
    }

    public function addResource(PostResource $postResource): self
    {
        if (!$this->resources->contains($postResource)) {
            $this->resources[] = $postResource;
            $postResource->setPost($this);
        }

        return $this;
    }

    /**
     * @return Collection<Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function setResourceUrls(array $resourceUrls): void
    {
        $this->resourceUrls = $resourceUrls;
    }

    public function getSharedPostId(): ?Uuid
    {
        return $this->sharedPostId;
    }

    public function getResourceUrls(): array
    {
        return $this->resourceUrls ?? [];
    }

    /**
     * @return Collection<PostResource>|null
     */
    public function getResources(): ?Collection
    {
        return $this->resources;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getLikes(): ?Collection
    {
        return $this->likes;
    }

    public function getSharesQuantity(): ?int
    {
        return $this->sharesQuantity;
    }

    public function setSharesQuantity(?int $sharesQuantity): void
    {
        $this->sharesQuantity = $sharesQuantity;
    }

    public function setSharedPost(?Post $sharedPost): void
    {
        $this->sharedPost = $sharedPost;
    }

    public function getSharedPost(): ?Post
    {
        return $this->sharedPost;
    }

    public function addHashtag(Hashtag $hashtag): self
    {
        if (!$this->hashtags->contains($hashtag)) {
            $this->hashtags[] = $hashtag;
        }

        return $this;
    }

    public function removeHashtag(Hashtag $hashtag): self
    {
        $this->hashtags->removeElement($hashtag);

        return $this;
    }

    public function getHashtags(): Collection
    {
        return $this->hashtags;
    }

    public function clearHashtags(): void
    {
        $this->hashtags->clear();
    }

    public function addMention(PostMention $mention): self
    {
        if (!$this->mentions->contains($mention)) {
            $this->mentions[] = $mention;
        }

        return $this;
    }

    public function getMentions(): Collection
    {
        return $this->mentions;
    }

    public function clearMentions(): void
    {
        $this->mentions->clear();
    }

    public function hasBeenSharedByUser(): bool
    {
        return $this->hasShared;
    }

    public function setHasShared(bool $hasShared): void
    {
        $this->hasShared = $hasShared;
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
