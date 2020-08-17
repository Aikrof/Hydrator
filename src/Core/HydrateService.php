<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Core;

use Aikrof\Hydrator\Components\Instance;
use Aikrof\Hydrator\Exceptions\ClassNotFoundException;

/**
 * Class HydrateService
 */
final class HydrateService extends Service
{
    /**
     * Create entity if string, and hydrate data from array to entity
     *
     * @param object|string     $entity
     * @param array             $data
     *
     * @return object|null
     */
    public function hydrate($entity, array $data): ?object
    {
        if (\is_object($entity)) {
            return $this->hydrateToEntity($entity, $data);
        }
        else if (\is_string($entity)) {
            return $this->createEntityAndHydrate($entity, $data);
        }

        return null;
    }

    /**
     * Create entity and hydrate data from array to this entity.
     *
     * @param string    $class
     * @param array     $data
     *
     * @return object
     *
     * @throws \ReflectionException
     * @throws \Aikrof\Hydrator\Exceptions\ClassNotFoundException
     * @throws \Aikrof\Hydrator\Exceptions\HydratorExeption
     */
    private function createEntityAndHydrate(string $class, array $data): object
    {
        if (!\class_exists($class)) {
            throw new ClassNotFoundException($class);
        }

        $entity = Instance::create($class);

        return $this->hydrateToEntity($entity, $data);
    }

    /**
     * Hydrate data from array to entity.
     *
     * @param object    $entity
     * @param array     $data
     *
     * @return object
     *
     * @throws \ReflectionException
     * @throws \Aikrof\Hydrator\Exceptions\HydratorExeption
     */
    private function hydrateToEntity(object $entity, array $data): object
    {
        $class = \get_class($entity);

        $this->validateEntity($entity, $class);

        /** @var \Aikrof\Hydrator\Core\Mapper[] */
        $mappings = $this->reflection->getMappings($class);

        $entityData = [];
        foreach ($mappings as $field => $mapping) {
            // check if entity has value or add default value from entity class.
            $defaultValue = $this->getFieldFromObject($entity, $field) ?? $mapping->getDefaultValue();
            $fieldValue = $data[$field] ?? $defaultValue;

            if ($mapping->isObject()) {
                if ($mapping->getType() === TypeEnum::ARRAY_OF_OBJECT) {
                    $nestedProperties = [];
                    foreach ($fieldValue as $key => $nestedValue) {
                        /**
                         * !!!to tests
                         */
                        $nestedEntity = $this->getFieldFromObject($entity, $field);
                        if ($nestedEntity === null || !\is_object($nestedEntity)) {
                            $nestedEntity = $mapping->getClassName();
                        }

                        $nestedProperties[$key] = $this->hydrate($nestedEntity, $nestedValue ?: []);
                    }

                    $value = $nestedProperties;
                }
                else {
                    $nestedEntity = $this->getFieldFromObject($entity, $field);
                    if ($nestedEntity === null || !\is_object($nestedEntity)) {
                        $nestedEntity = $mapping->getClassName();
                    }

                    $value = $this->hydrate($nestedEntity, $fieldValue ?: []);
                }
            }
            else {
                $value = $fieldValue;
            }

            // check value type and object field type
            $entityData[$field] = $mapping->checkValueType($value) === true ? $value :  $mapping->getDefaultValue();
        }

        $this->fillEntityFields($entity, $entityData);

        return $entity;
    }
}
