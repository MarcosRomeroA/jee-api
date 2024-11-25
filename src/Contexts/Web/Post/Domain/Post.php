<?php declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain;

use App\Contexts\Shared\Domain\Aggregate\AggregateRoot;
use App\Contexts\Shared\Domain\Traits\Timestamps;
use App\Contexts\Shared\Domain\ValueObject\CreatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\UpdatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Persistence\Doctrine\ContainsNullableEmbeddable;
use App\Contexts\Web\Post\Domain\Events\PostCommentedDomainEvent;
use App\Contexts\Web\Post\Domain\Events\PostCreatedDomainEvent;
use App\Contexts\Web\Post\Domain\ValueObject\BodyValue;
use App\Contexts\Web\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;

#[ContainsNullableEmbeddable]
#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', length: 36)]
    private Uuid $id;

    #[Embedded(class: BodyValue::class, columnPrefix: false)]
    private BodyValue $body;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'post', cascade: ['persist', 'remove'])]
    private ?Collection $comments;

    #[ORM\OneToMany(targetEntity: PostResource::class, mappedBy: 'post', cascade: ['persist', 'remove'])]
    private ?Collection $resources;

    private array $resourceUrls;

    use Timestamps;

    public function __construct(
        Uuid $id,
        BodyValue $body,
        User $user,
    )
    {
        $this->id = $id;
        $this->body = $body;
        $this->user = $user;
        $this->createdAt = new CreatedAtValue();
        $this->updatedAt = new UpdatedAtValue($this->createdAt->value());
        $this->comments = new ArrayCollection();
        $this->resources = new ArrayCollection();
    }

    public static function create(
        Uuid $id,
        BodyValue $body,
        User $user,
        array $resources
    ): self
    {
        $post = new self($id, $body, $user);

        $post->record(new PostCreatedDomainEvent(
            $id,
            $resources
        ));

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
            $this->record(new PostCommentedDomainEvent(
                $this->id,
                ["commentId" => $comment->getId()]
            ));
        }

        return $this;
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

    public function getResourceUrls(): array
    {
        return $this->resourceUrls;
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
}