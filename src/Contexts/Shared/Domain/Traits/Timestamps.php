<?php declare(strict_types=1);

namespace App\Contexts\Shared\Domain\Traits;

use App\Contexts\Shared\Domain\ValueObject\CreatedAtValue;
use App\Contexts\Shared\Domain\ValueObject\DeletedAtValue;
use App\Contexts\Shared\Domain\ValueObject\UpdatedAtValue;
use App\Contexts\Shared\Infrastructure\Persistence\Doctrine\Nullable;
use Doctrine\ORM\Mapping\Embedded;

trait Timestamps
{
    #[Embedded(class: CreatedAtValue::class, columnPrefix: false)]
    private CreatedAtValue $createdAt;

    #[Embedded(class: UpdatedAtValue::class, columnPrefix: false)]
    private UpdatedAtValue $updatedAt;

    #[Nullable]
    #[Embedded(class: DeletedAtValue::class, columnPrefix: false)]
    private ?DeletedAtValue $deletedAt;

    public function getCreatedAt(): CreatedAtValue
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): UpdatedAtValue
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?DeletedAtValue
    {
        return $this->deletedAt;
    }
}