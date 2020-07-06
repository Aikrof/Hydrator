<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator\Interfaces
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Interfaces;

/**
 * Interface ServiceHydratorInterface
 */
interface ServiceHydratorInterface
{
    /**
     * Extract data from object to array
     *
     * @param object $entity Object entity.
     * @param array  $exclude define list of fields that should be excluded.
     * @param bool   $hideNullProperties If set `$hideNullProperties` = false null properties will be mapped to array only if:
     *      1. If in field annotation we set null like compound property (string|null).
     *      2. If we haven't any annotation for field.
     *      3. If in field annotation we have `@internal` tag.
     *
     * @return array
     *
     * @throws \ReflectionException
     * @throws \Aikrof\Hydrator\Exceptions\HydratorExeption
     */
    public function extract(object $entity, array $exclude, bool $hideNullProperties): array;

    /**
     * Hydrate data from array to object.
     *
     * @param object|string     $entity
     * @param array             $data
     *
     * @return object|null
     *
     * @throws \ReflectionException
     * @throws \Aikrof\Hydrator\Exceptions\ClassNotFoundException
     * @throws \Aikrof\Hydrator\Exceptions\HydratorExeption
     */
    public function hydrate($entity, array $data): ?object;
}