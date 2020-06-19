<?php
/**
 * @link http://www.bintime.com
 * @copyright 2017 (c) Bintime
 * @package Gepard\Hydrator
 * @author Rusavskiy Vitaliy <rusavskiy.v@bintime.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Tests;

use Exception;

use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use ReflectionException;


/**
 * Class Reflection
 */
class Schema
{
    /** @var \ArrayIterator[] */
    private static $classMappings = [];

    /** @var CacheInterface */
    private $cache;

    /** @var Reflection */
    private $reflection;

    /**
     * Schema constructor.
     *
     * @param CacheInterface      $cache Cache for schema.
     * @param ReflectionInterface $reflection Reflection.
     */
    public function __construct()
    {
    }

    /**
     * Create object entity or other class.
     *
     * @param string $class Name entity.
     *
     * @return object
     * @throws ReflectionException
     */
    public function createObject(string $class)
    {
        return $this->reflection->createObject($this->filterNameClass($class));
    }

    /**
     * Get scheme mapping entity.
     *
     * @param string|object $object Entity object or class name.
     *
     * @return \ArrayIterator
     * @throws HydratorException
     * @throws ReflectionException
     */
    public function getSchemaMapping($object): \ArrayIterator
    {
        if (empty($object)) {
            throw new Exception('Empty $object!');
        }

        $class = $object;
        if (\is_object($object)) {
            $class = \get_class($object);
        }

//        $class = $this->filterNameClass($class);
//        if (!$this->hasSchemaMapping($class)) {
            $this->createSchemaMapping($class);
//        }

        return self::$classMappings[$class];
    }

    /**
     * Check for existence schema.
     *
     * @param string $class Class name for check.
     *
     * @return boolean
     */
    private function hasSchemaMapping(string $class): bool
    {
        return isset(self::$classMappings[$class]);
    }

    /**
     * Recursive create schema classes.
     *
     * @param string $class Class name for create schema.
     *
     * @return void
     * @throws \Gepard\Hydrator\Exceptions\HydratorException
     * @throws ReflectionException
     */
    private function createSchemaMapping(string $class): void
    {
        $class = $this->filterNameClass($class);

        $reflectionClass = new \ReflectionClass($class);
        $mappingOrigin = new FieldMapping();
        $factory = DocBlockFactory::createInstance();

        $defaultValues = $reflectionClass->getDefaultProperties();

        $mappings = [];
        foreach ($reflectionClass->getProperties() as $property) {
            $doc = $property->getDocComment();
            if ($doc === false) {
                throw new InvalidArgumentException(
                    'Phpdoc cannot by empty for property:"' . $property->getName()
                    . '" in class: ' . $property->getDeclaringClass()
                );
            }

            try {
                $docBlock = $factory->create($doc);
            } catch (Exception $e) {
                throw new InvalidArgumentException(
                    $e->getMessage() . \PHP_EOL . ' ~ Phpdoc is wrong for property: "' .
                    $property->getName() . '" in class: ' . $property->getDeclaringClass()->getName()
                );
            }

            if ($docBlock->hasTag('internal')) {
                continue;
            }

            if ($docBlock->hasTag('var')) {
                $tagsVar = (array)$docBlock->getTagsByName('var');
                /**
                 * @var \phpDocumentor\Reflection\DocBlock\Tags\Var_ $tag
                 * @see \phpDocumentor\Reflection\DocBlock\StandardTagFactory::$tagHandlerMappings
                 */
                $tag = \current($tagsVar);
                unset($tagsVar);
                /**
                 * @see \phpDocumentor\Reflection\TypeResolver::$keywords
                 */
                $type = $tag->getType();
                unset($tag);

                $typeToString = (string)$type;
                $nullProperty = false;

                if ($type instanceof Compound) {
                    $typeToString = null;
                    $OldTypeName = $type->__toString();

                    foreach ($type as $dataType) {
                        if ($dataType instanceof Null_) {
                            $nullProperty = true;
                            continue;
                        }

                        if ($typeToString === null) {
                            $typeToString = $dataType->__toString();
                            $type = $dataType;
                            continue;
                        }

                        throw new HydratorException(
                            $class . " - property `{$property->getName()}` must be of one data-type (including nullable). Current value is incorrect: '{$OldTypeName}'"
                        );
                    }
                }

                $mapping = clone $mappingOrigin;
                $mapping->setField($property->name);

                if ($type instanceof Object_) {
                    $mapping->setClassName($typeToString);
                    $mapping->objectAsNull = $nullProperty;
                    if ($class !== $this->filterNameClass($typeToString)) {
                        $this->createSchemaMapping($mapping->getClassName());
                    }
                    $typeToString = FieldMapping::TYPE_OBJECT;
                } elseif ($type instanceof Array_
                    && $fqsen = $type->getValueType()
                ) {

                    if ($fqsen instanceof Object_) {
                        $mapping->setClassName($fqsen->__toString());
                        if ($class !== $this->filterNameClass($mapping->getClassName())) {
                            $this->createSchemaMapping($mapping->getClassName());
                        }
                        $typeToString = FieldMapping::TYPE_ARRAY_OBJECT;
                    } elseif ($fqsen instanceof String_) {
                        $typeToString = FieldMapping::TYPE_ARRAY_STRING;
                    } elseif ($fqsen instanceof Integer) {
                        $typeToString = FieldMapping::TYPE_ARRAY_INT;
                    } elseif ($fqsen instanceof Float_) {
                        $typeToString = FieldMapping::TYPE_ARRAY_FLOAT;
                    }
                }

                $mapping->setType($typeToString);
                $mapping->setDefaultPropertyValue($defaultValues[$property->name]);

                $mappings[$property->name] = $mapping;
            }
        }

        $this->addMapping($class, new \ArrayIterator($mappings));
        $this->setCachedSchema($class);
    }

    /**
     * Add ready mapping is storage property.
     *
     * @param string         $class Class name and key in storage.
     * @param \ArrayIterator $mappings Ready mapping in format ArrayIterator.
     *
     * @return void
     */
    private function addMapping(string $class, \ArrayIterator $mappings): void
    {
        self::$classMappings[$class] = $mappings;
    }

    /**
     * Filtered class name.
     *
     * @param string $class Class name for filter.
     *
     * @return string
     */
    private function filterNameClass(string $class): string
    {
        return $class[0] === '\\' ? \trim($class, '\\') : $class;
    }

    /**
     * Check cache schema.
     *
     * @param string $class Name class.
     *
     * @return boolean
     */
    private function hasCachedSchema(string $class): bool
    {
        return $this->cache->exists($this->keyCacheSchema($class));
    }

    /**
     * @param string $class Name class.
     *
     * @return \ArrayIterator
     */
    private function getCachedSchema(string $class): \ArrayIterator
    {
        $dataCacheSerialized = $this->cache->get($this->keyCacheSchema($class));
        $iterator = new \ArrayIterator;
        $iterator->unserialize($dataCacheSerialized);

        return $iterator;
    }

    /**
     * Cache all FieldMappings for $class.
     *
     * @param string $class Name class.
     *
     * @return void
     */
    private function setCachedSchema(string $class): void
    {
        if (\array_key_exists($class, self::$classMappings)) {

        }
    }

    /**
     * Create check key.
     *
     * @param string $class Name class.
     *
     * @return string
     */
    private function keyCacheSchema(string $class): string
    {
        return \md5(__CLASS__ . $class);
    }
}
