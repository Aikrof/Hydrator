<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator;

use Aikrof\Hydrator\Components\Instance;
use Aikrof\Hydrator\Entity\EntityInterface;
use Aikrof\Hydrator\Interfaces\ServiceHydratorInterface;

/**
 * Class Hydrator
 */
class Hydrator
{
    /**
     * @var \Aikrof\Hydrator\Interfaces\ServiceHydratorInterface
     */
    private static $serviceHydrator;

    /**
     * @return \Aikrof\Hydrator\Interfaces\ServiceHydratorInterface
     */
    private static function getServiceHydrator(): ServiceHydratorInterface
    {
        if (!self::$serviceHydrator){
            self::$serviceHydrator = Instance::create(ServiceHydratorInterface::class);
        }

        return self::$serviceHydrator;
    }

    /**
     * Extract data from object to array.
     *
     * @param object|string    $entity
     * @param array            $exclude define list of fields that should be excluded.
     * @param bool             $hideNullProperties If set `$hideNullProperties` = true, null properties will be mapped to array only if:
     *        1. In field annotation we set null like compound property (string|null).
     *        2. We haven't any annotation for field.
     *        3. In field annotation we have `@internal` tag.
     *
     *
     * @return array
     *
     * @throws \Aikrof\Hydrator\Exceptions\HydratorExeption
     */
    public static function extract($entity, array $exclude = [], bool $hideNullProperties = false): array
    {
        if (\is_object($entity)){
            return self::getServiceHydrator()->extract($entity, $exclude, $hideNullProperties);
        }

        if (\is_string($entity) || \in_array(EntityInterface::class, \class_implements((string)$entity), true)){
            $object = Instance::create((string)$entity);
            return self::getServiceHydrator()->extract($object, $exclude, $hideNullProperties);
        }

        return [];
    }

    /**
     * Hydrate data from array to object.
     *
     * @param object|string     $entity
     * @param array             $data
     *
     * @return object|null
     *
     * @throws \Aikrof\Hydrator\Exceptions\ClassNotFoundException
     * @throws \Aikrof\Hydrator\Exceptions\HydratorExeption
     */
    public static function hydrate($entity, array $data): ?object
    {
        return self::getServiceHydrator()->hydrate($entity, $data);
    }
}