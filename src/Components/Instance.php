<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator\Components
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Components;

use Aikrof\Hydrator\Exceptions\ClassNotFoundException;
use Aikrof\Hydrator\Components\FileManager\NativeFileManager;
use Aikrof\Hydrator\Interfaces\CacheInterface;

/**
 * Class Instance
 */
class Instance
{
    /**
     * Create object from container, if object is not exist create from native container.
     *
     * @param string $class
     *
     * @return object
     */
    public static function create(string $class): object
    {
        if (!empty($object = self::ensure($class))) {
            return $object;
        }

        $container = NativeFileManager::getNativeContainer();

        if (empty($container[$class]) && !\class_exists($class)){
            throw new ClassNotFoundException($class);
        }

        $className = !empty($container[$class]) ? $container[$class] : $class;

        return new $className;
    }

    /**
     * Create object from Yii or Laravel container.
     *
     * @param string $interface
     *
     * @return object|null
     */
    public static function ensure(string $interface): ?object
    {
        $object = null;

        if (\class_exists('\Yii') && isset(\Yii::$container)) {
            $container = \Yii::$container;

            if ($container->hasSingleton($interface) || \array_key_exists($interface, $container->getDefinitions())) {
                $object = \Yii::createObject($interface);
            }
        }
        else if (\class_exists(\Illuminate\Container\Container::class)) {
            $container = \Illuminate\Container\Container::getInstance();

            if ($container->has($interface)) {
               $object = $container->make($interface);
            }
        }

        if (!empty($object) && $object instanceof $interface) {
            return $object;
        }

        return null;
    }
}
