<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Core;

use Aikrof\Hydrator\Components\Instance;
use Aikrof\Hydrator\Exceptions\HydratorExeption;
use Aikrof\Hydrator\Interfaces\ReflectionInterface;

/**
 * Class Service
 */
abstract class Service
{
    /**
     * @var \Aikrof\Hydrator\Core\Reflection
     */
    protected $reflection;

    /**
     * Extract constructor.
     *
     * @param \Aikrof\Hydrator\Core\Reflection|null $reflection
     */
    public function __construct(Reflection $reflection = null)
    {
        $this->reflection = $reflection ?: Instance::create(Reflection::class);
    }

    /**
     * @param object    $entity
     * @param string    $class
     *
     * @return void
     *
     * @throws \Aikrof\Hydrator\Exceptions\HydratorExeption
     */
    protected function validateEntity(object $entity, string $class): void
    {
        /** @var bool $isObject */
        $isObject = \is_object($entity);

        if (!$isObject) {
            throw new HydratorExeption("Entity mast be an object {$entity}");
        }

        if ($isObject && empty($entity)) {
            throw new HydratorExeption("Cannot get properties from: {$class}.");
        }
    }

    /**
     * Extract all values from the given object.
     *
     * @param object $entity
     * @param array  $objectFields
     *
     * @return array|null
     */
    protected function extractObjectValues(object $entity, array $objectFields): ?array
    {
        return (function ($objectFields) {
            $data = [];
            foreach ($objectFields as $field) {
                $data[$field] = $this->{$field};
            }

            return $data;
        })->call($entity, $objectFields);
    }

    /**
     * Exclude nested fields from array.
     *
     * @param array $exclude
     * @param array $data
     *
     * @return bool
     */
    protected function excludeNestedFields(array $exclude, array &$data): bool
    {
        $current = \current($exclude);
        $next = \next($exclude);

        if (!isset($data[$current])) {
            return false;
        }

        if ($next === false) {
            unset($data[$current]);
            return true;
        }

        return $this->excludeNestedFields($exclude, $data[$current]);
    }

    /**
     * @param object $entity
     * @param array  $data
     *
     * @return void
     */
    protected function fillEntityFields(object $entity, array $data): void
    {
        (function ($data) {
            foreach ($data as $field => $value) {
                $this->{$field} = $value;
            }
        })->call($entity, $data);
    }
}
