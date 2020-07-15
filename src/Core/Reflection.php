<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator\Core
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Core;

use Aikrof\Hydrator\Exceptions\HydratorExeption;
use Aikrof\Hydrator\Interfaces\CacheInterface;
use Aikrof\Hydrator\Components\Instance;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionProperty;

/**
 * Class Reflection
 */
class Reflection
{
    public const PUBLIC = ReflectionProperty::IS_PUBLIC;
    public const PROTECTED = ReflectionProperty::IS_PROTECTED;
    public const PRIVATE = ReflectionProperty::IS_PRIVATE;

    /** @var self */
    private static $mappings;

    /**
     * @var int
     */
    public $access;

    /**
     * @var \Aikrof\Hydrator\Interfaces\CacheInterface|null
     */
    private $cache;

    /**
     * Reflection constructor.
     *
     * @param \Aikrof\Hydrator\Interfaces\CacheInterface|null $cache
     */
    public function __construct(CacheInterface $cache = null, $config = [])
    {
        $this->cache = $cache ?: Instance::ensure(CacheInterface::class);
        $this->access = $config['access'] ?? self::PUBLIC | self::PROTECTED;
    }

    /**
     * @param string $class
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    public function getMappings(string $class): array
    {
        if (empty(self::$mappings[$class])) {
            if ($this->isCached($class)) {
                $this->getMappingsFromCache($class);
            }
            else {
                $this->createMappings($class);
                $this->setMappingsToCache($class);
            }
        }

        return self::$mappings[$class];
    }

    /**
     * @param string $class
     *
     * @throws \Aikrof\Hydrator\Exceptions\HydratorExeption
     * @throws \ReflectionException
     */
    private function createMappings(string $class): void
    {
        $reflectionClass = new \ReflectionClass($class);

        /** @var ReflectionProperty $properties */
        $properties = $reflectionClass->getProperties($this->access);
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

                    $this->getMappings($annotation->getClassName());
                }
            }
            else {
                // If we haven't annotation for this field, we will set nullable for default property of this field
                $mapping->setIsNullDefaul(true);
            }

            $mapping->setDefaultValue($values[$property->getName()]);
            $schemas[$property->name] = $mapping;
        }

        self::$mappings[$class] = $schemas;
    }

    /**
     * Search schema in cache
     *
     * @param string $class
     *
     * @return bool
     */
    private function isCached(string $class): bool
    {
        if (!empty($this->cache)) {
            try {
                return (bool)$this->cache->get($class);
            } catch (\Exception $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * Get mappings from cache.
     *
     * @param string $class
     */
    private function getMappingsFromCache(string $class): void
    {
        $schema = $this->cache->get($class);

        self::$mappings[$class] = \unserialize($schema);
    }

    /**
     * Set mappings to cache.
     *
     * @param string $class
     */
    private function setMappingsToCache(string $class): void
    {
        if (!empty($this->cache)) {
            try {
                $schema = \serialize(self::$mappings[$class]);
                $this->cache->set($class, $schema);
            } catch (\Exception $e) {
            }
        }
    }
}
