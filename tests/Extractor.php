<?php
/**
 * @link http://www.bintime.com
 * @copyright 2017 (c) Bintime
 * @package Gepard\Hydrator
 * @author Rusavskiy Vitaliy <rusavskiy.v@bintime.com>
 * @date 02.11.17
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Tests;

use Gepard\Core\Helpers\ValueHelper;
use Gepard\Hydrator\Interfaces\ClosureInterface;
use Gepard\Hydrator\Interfaces\ExtractorInterface;
use Gepard\Hydrator\Interfaces\SchemaInterface;
use InvalidArgumentException;

/**
 * Class Extractor
 */
final class Extractor
{
    /** @var Closure */
    private $closure;

    /** @var Schema */
    private $schema;


    /**
     * Extractor constructor.
     *
     * @param ClosureInterface $closure Closure object for fill and extract data.
     * @param SchemaInterface  $schema Schema entity classes.
     */
    public function __construct(ClosureInterface $closure = null, SchemaInterface $schema = null)
    {
        $this->closure = $closure;
        $this->schema = new Schema();
    }

    /**
     * Extract data with entity object.
     *
     * @param object $entity Object entity.
     * @param array  $options Configuration to adjust data to extract from entity. Supports keys 'exclude' and
     *     'include' to define list of fields that should be excluded or included only. If specified both - 'include'
     *     has more priority.
     * @param bool   $nestedObject If nested object - filtered fields.
     *
     * @return array
     * @throws \ReflectionException
     * @throws \Gepard\Hydrator\Exceptions\HydratorException
     */
    public function extract($entity, array $options = [], $nestedObject = false): array
    {
        $mappings = $this->schema->getSchemaMapping($entity);

//        $options[ExtractorInterface::INCLUDE] = \array_flip($options[ExtractorInterface::INCLUDE] ?? []);
//        $options[ExtractorInterface::EXCLUDE] = \array_flip($options[ExtractorInterface::EXCLUDE] ?? []);

        // Ignore 'exclude' list if 'include' was specified
//        if (!empty($options[ExtractorInterface::INCLUDE])) {
//            $options[ExtractorInterface::EXCLUDE] = [];
//        }
        $includeAll = true;

        $callProperties = [];
        $objectProperties = [];
        /** @var FieldMapping $fieldMapping */
        foreach ($mappings as $property => $fieldMapping) {
            if ((!$includeAll && !\array_key_exists($property, $options))
                || \array_key_exists($property, $options)
            ) {
                continue;
            }
            $callProperties[] = $property;
            if ($fieldMapping->isKindOfObject()) {
                $objectProperties[] = $property;
            }
        }
        unset($property, $fieldMapping);

        $data = $this->extractFieldsValue($entity, $callProperties);

        if ($nestedObject === true) {
            $data = \array_filter(
                $data,
                static function ($v, $k) {
                    return !(self::isEmpty($v) || (\is_array($v) && empty($v)));
                },
                ARRAY_FILTER_USE_BOTH
            );
        }

        if (!empty($data) && !empty($objectProperties)) {
            foreach ($objectProperties as $property) {
                if (!\array_key_exists($property, $data)) {
                    continue;
                }

                /** @var FieldMapping $field */
                $fieldMapping = $mappings[$property];

                $fieldType = $fieldMapping->type;
                /** Recursive extract objects */
                if ($fieldType === FieldMapping::TYPE_OBJECT) {
                    $result = \is_array($data[$property])
                        ? $data[$property]
                        : $this->extractNestedObject($fieldMapping->getClassName(), $data[$property]);
                    $data[$property] = $result ?: null;
                } elseif ($fieldType === FieldMapping::TYPE_ARRAY_OBJECT) {
                    $listObjectsChildren = [];
                    foreach ($data[$property] ?: [] as $key => $dataNested) {
                        $result = \is_array($dataNested)
                            ? $dataNested
                            : $this->extractNestedObject($fieldMapping->getClassName(), $dataNested);

                        if (!empty($result)) {
                            $listObjectsChildren[$key] = $result;
                        }
                    }
                    $data[$property] = $listObjectsChildren;
                }
            }
        }

        return $data;
    }

    /**
     * Recursive extract objects
     *
     * @param string      $class Name entity class.
     * @param null|object $entity Entity if exist.
     *
     * @param bool        $nestedObject
     *
     * @return array
     * @throws \ReflectionException
     * @throws \Gepard\Hydrator\Exceptions\HydratorException
     */
    private function extractNestedObject(string $class, $entity, $nestedObject = true): array
    {
//        //TODO: not check in tests!!!
//        if (null === $entity) {
//            $entity = $this->schema->createObject($class);
//        }

        if (!\is_object($entity)) {
            if ($nestedObject) {
                return [];
            }

            throw new InvalidArgumentException('Wrong param entity! $entity must be object!');
        }

        return $this->extract($entity, [], $nestedObject);
    }

    public static function isEmpty($value): bool
    {
        return null === $value || '' === $value || $value === [];
    }

    /**
     * Extract data with entity.
     *
     * @param object $object Entity for extract.
     * @param array  $properties Collection field entity.
     *
     * @return mixed
     */
    private function extractFieldsValue($object, array $properties)
    {
        $f = function (array $properties) {
            $result = [];
            foreach ($properties as $name) {
                $value = null;
                try {
                    $value = $this->{$name};
                } catch (Throwable $t) {
                    \Yii::error($t->getMessage());
                } finally {
                    $result[$name] = $value;
                }
            }

            return $result;
        };
        return $f->call($object, $properties);
    }
}
