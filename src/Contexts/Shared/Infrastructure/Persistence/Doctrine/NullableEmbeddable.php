<?php declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Persistence\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use ReflectionObject;

#[AsDoctrineListener(event: Events::postLoad)]
final class NullableEmbeddable
{
    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        $reflector = new \ReflectionClass($entity);
        $attributes = $reflector->getAttributes(ContainsNullableEmbeddable::class);
        if (empty($attributes)) {
            return;
        }

        $objectReflection = new ReflectionObject($entity);
        foreach ($objectReflection->getProperties() as $property) {
            $propertyAttributes = $property->getAttributes(Nullable::class);
            if (empty($propertyAttributes)) {
                continue;
            }
            $value = $property->getValue($entity);
            if ($this->allPropertiesAreNull($value)) {
                $property->setValue($entity, null);
            }
        }
    }

    private function allPropertiesAreNull(mixed $object): bool
    {
        $objectReflection = new ReflectionObject($object);
        foreach ($objectReflection->getProperties() as $property) {
            if ($property->isInitialized($object) && null !== $property->getValue($object)) {
                return false;
            }
        }

        return true;
    }
}