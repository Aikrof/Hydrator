<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator\Traits
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types=1);

namespace Aikrof\Hydrator\Traits;

use Aikrof\Hydrator\Entity\EntityInterface;
use Aikrof\Hydrator\Hydrator;

/**
 * Trait HydratorTrait
 */
trait HydratorTrait
{
    public function extractData(EntityInterface $entity = null): array
    {
        if (!$entity && !$this instanceof EntityInterface){
            throw new \InvalidArgumentException('Entity not found.');
        }

        return Hydrator::extract($entity ?: $this);
    }

    public function hydrateData(array $data, $entity = null): object
    {
        if (!$entity && $this instanceof EntityInterface){
            Hydrator::hydrate($data, $this);

            return $this;
        }

        return Hydrator::hydrate($data, (string)$entity);

//        return Hydrator::getHydrator()->hydrate($data, $entity ?: $this);
//        if (!$entity && $this instanceof EntityInterface){
//            ServiceHydrator::getHydrator()->hydrateFromObject($data, $this);
//        }
//        else if (\is_string($entity) || \in_array(EntityInterface::class, \class_implements((string)$entity), true)){
//            ServiceHydrator::getHydrator()->hydrate($data, (string)$entity);
//        }
//        else if (\is_object($entity) && $entity instanceof EntityInterface){
//            ServiceHydrator::getHydrator()->hydrateFromObject($data, $entity);
//        }
    }
}