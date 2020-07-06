<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Core;



/**
 * Class ExtractService
 */
final class ExtractService extends Service
{
    /**
     * @param object $entity Object entity.
     * @param array  $exclude define list of fields that should be excluded.
     * @param bool   $hideNullProperties If set `$hideNullProperties` = false null properties will be mapped to array only if:
     *      1. If in field annotation we set null like compound property (string|null).
     *      2. If we haven't any annotation for field.
     *      3. If in field annotation we have `@internal` tag.
     * @param bool   $recursiveCall
     *
     * @return array
     *
     * @throws \ReflectionException
     * @throws \Aikrof\Hydrator\Exceptions\HydratorExeption
     */
    public function extract(
        object $entity,
        array $exclude,
        bool $hideNullProperties,
        bool $recursiveCall = false
    ): array {
        $class = \get_class($entity);

        $this->validateEntity($entity, $class);

        $exclude = $recursiveCall === false ? array_flip($exclude) : $exclude;

        /** @var \Aikrof\Hydrator\Core\Mapper[] $mappings */
        $mappings = $this->reflection->getMappings($class);

        if (!$objectFields = \array_keys($mappings)) {
            return [];
        }

        $data = $this->extractObjectValues($entity, $objectFields);

        $result = [];
        foreach ($data as $field => $property) {
            // Exclude fields
            if (!$recursiveCall && \array_key_exists($field, $exclude)) {
                unset($exclude[$field]);
                continue;
            }

            $mapping = $mappings[$field];

            $value = null;
            if (!empty($property)) {
                if ($mapping->isObject()) {
                    if ($mapping->getType() === TypeEnum::ARRAY_OF_OBJECT) {
                        $nestedProperties = [];
                        foreach ($property as $key => $nestedObject) {
                            $nestedProperties[$key] = $this->extract($nestedObject, $exclude, $hideNullProperties, true);
                        }

                        $value = $nestedProperties;
                    }
                    else {
                        $value = $this->extract($property, $exclude, $hideNullProperties, true);
                    }
                }
                else {
                    $value = $property;
                }
            }

            if (empty($value)) {
                if ($hideNullProperties === true && empty($mapping->getDefaultValue())) {
                    continue;
                }

                $value = $mapping->getDefaultValue();
            }

            // Exclude nested fields
            if (!$recursiveCall && !empty($exclude) && \is_array($value)) {
                $fieldLength = \strlen($field);
                foreach ($exclude as $excludeKey => $excludeVal) {
                    if (strncmp($excludeKey, $field, $fieldLength) === 0) {
                        $nested = \explode('.', $excludeKey);
                        unset($nested[0]);

                        $isValueExcluded = false;
                        // if we have nested arrays, we need to exclude fields from all of this arrays
                        if (isset($value[0])) {
                            foreach ($value as $dataKey => $dataValue) {
                                $isValueExcluded = $this->excludeNestedFields($nested, $value[$dataKey]);
                                if ($isValueExcluded === false) {
                                    break;
                                }
                            }
                        }
                        else {
                            $isValueExcluded = $this->excludeNestedFields($nested, $value);
                        }

                        if ($isValueExcluded === true) {
                            unset($exclude[$excludeKey]);
                        }
                    }
                }
            }

            $result[$field] = $value;
        }

        return $result;
    }
}