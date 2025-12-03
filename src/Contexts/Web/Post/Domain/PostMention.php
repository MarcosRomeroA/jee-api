<?php

declare(strict_types=1);

namespace App\Contexts\Web\Post\Domain;

use App\Contexts\Shared\Domain\ValueObject\CreatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'post_mention')]
#[ORM\UniqueConstraint(name: 'unique_post_mention', columns: ['post_id', 'user_id'])]
class PostMention
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'mentions')]
    #[ORM\JoinColumn(name: 'post_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Post $post;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    private function __construct(
        Uuid $id,
        Post $post,
        User $user,
    ) {
        $this->id = $id;
        $this->post = $post;
        $this->user = $user;
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function create(Uuid $id, Post $post, User $user): self
    {
        return new self($id, $post, $user);
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
