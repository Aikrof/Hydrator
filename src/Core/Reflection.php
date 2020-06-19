<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator\Core
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Core;

use Aikrof\Hydrator\Exceptions\HydratorExeption;
use Aikrof\Hydrator\Interfaces\ReflectionInterface;
use Aikrof\Hydrator\Interfaces\CacheInterface;
use Aikrof\Hydrator\Components\Instance;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionProperty;

/**
 * Class Reflection
 */
class Reflection implements ReflectionInterface
{
    protected const PUBLIC = ReflectionProperty::IS_PUBLIC;
    protected const PROTECTED = ReflectionProperty::IS_PROTECTED;
    protected const PRIVATE = ReflectionProperty::IS_PRIVATE;

    /** @var self */
    private static $mappings;

    /**
     * @var int
     */
    private static $allow = self::PUBLIC | self::PROTECTED;

    /**
     * @var CacheInterface|null
     */
    private $cache;

    public function __construct(CacheInterface $cache = null)
    {
        $this->cache = $cache ?: Instance::createIfExist(CacheInterface::class);
    }

    /**
     * @param string $class
     *
     * @return array
     * @throws \ReflectionException
     */
    public function getMappings(string $class): array
    {
        if (empty(self::$mappings[$class])) {
            if ($this->isCached($class)) {
                $this->setMappingsFromCache($class);
            }
            else {
                $this->createMappings($class);
            }
        }

        return self::$mappings[$class];
    }

    /**
     * @param string $class
     *
     * @throws \ReflectionException
     */
    private function createMappings(string $class): void
    {
        $reflectionClass = new \ReflectionClass($class);

        /** @var ReflectionProperty $properties */
        $properties = $reflectionClass->getProperties(self::$allow);
        $values = $reflectionClass->getDefaultProperties();

        if (empty($properties)) {
            throw new HydratorExeption("Cannot get properties from: `{$class}`, `{$class}` has no properties.");
        }

        /** @var \phpDocumentor\Reflection\DocBlockFactory $factory */
        $factory = DocBlockFactory::createInstance();

        $mapper = new Mapper($class);
        $schemas = [];
        foreach ($properties as $property){
            /**  @var \Aikrof\Hydrator\Core\Annotation $annotation */
            $annotation = Annotation::getDoc($factory, $property);

            if ($annotation !== null && $annotation->isIgnore()) {
                continue;
            }

            /** @var \Aikrof\Hydrator\Core\Mapper $mapping */
            $mapping = clone $mapper;

            $mapping->setField($property->getName());

            if ($annotation !== null) {
                $mapping->setType($annotation->getType());
                $mapping->setIsNullDefaul($annotation->isIncludeNullable());

                if ($annotation->isClass()) {
                    $mapping->setClassName($annotation->getClassName());
                    $mapping->setIsObject(true);

                    $this->createMappings($annotation->getClassName());
                }
            }
            else {
                // If we haven't annotation of this field, we will set nullable for default property of this field
                $mapping->setIsNullDefaul(true);
            }

            /**
             * @todo put $schema in to cache
             */
            $mapping->setDefaultValue($values[$property->getName()]);
            $schemas[$property->name] = $mapping;
        }

        self::$mappings[$class] = $schemas;
    }

    /**
     * @todo search schema in cache
     *
     * @param string $class
     *
     * @return bool
     */
    private function isCached(string $class): bool
    {
        if ($this->cache) {
            //TO DO
        }

        return false;
    }

    /**
     * @todo get mappings from cache and set them in to self::$mappings[$class]
     *
     * @param string $class
     */
    private function setMappingsFromCache(string $class): void
    {
        // TO DO
    }
}