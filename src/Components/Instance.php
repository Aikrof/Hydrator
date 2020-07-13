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
 *
 * @todo change logic of this class
 */
class Instance
{
    public static function create(string $class)
    {
        $container = NativeFileManager::getNativeContainer();

        if (empty($container[$class]) && !\class_exists($class)){
            throw new ClassNotFoundException($class);
        }

        $className = !empty($container[$class]) ? $container[$class] : $class;

        return new $className;
    }

    public static function ensure(string $interface): ?object
    {
        $object = null;

        if (\class_exists('\Yii') && isset(\Yii::$container)) {
            $container = \Yii::$container;
            if ($container->hasSingleton($interface) || \array_key_exists($interface, $container->getDefinitions())) {
                $object = \Yii::createObject($interface);
            }
        }

        if (!empty($object) && $object instanceof $interface) {
            return $object;
        }

        return null;
    }
}