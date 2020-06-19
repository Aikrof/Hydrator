<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types=1);

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
     * @param object|string    $entity
     * @param bool             $hideNullProperties If set `$hideNullProperties` = true, null properties will be mapped to array only if:
     *        1. In field annotation we set null like compound property (string|null).
     *        2. We haven't any annotation for field.
     *        3. In field annotation we have `@internal` tag.
     *
     * @param array            $exclude define list of fields that should be excluded.
     *
     * @return array
     */
    public static function extract($entity, bool $hideNullProperties = false, array $exclude = []): array
    {
        if (\is_object($entity)){
            return self::getServiceHydrator()->extractFromEntity($entity, $hideNullProperties, $exclude);
        }

        if (\is_string($entity) || \in_array(EntityInterface::class, \class_implements((string)$entity), true)){
            $object = Instance::create((string)$entity);
            return self::getServiceHydrator()->extractFromEntity($object, $hideNullProperties, $exclude);
        }

        return [];
    }

    /**
     * @param array             $data
     * @param object|string     $entity
     *
     * @return object
     */
    public static function hydrate(array $data, $entity): object
    {
        if (\is_object($entity)) {
            return self::getServiceHydrator()->hydrateToEntity($data, $entity);
        }

        if (\is_string($entity)) {
            return self::getServiceHydrator()->createEntityAndHydrate($data, $entity);
        }
    }
}