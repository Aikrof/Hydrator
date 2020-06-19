<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator\Components
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types=1);

namespace Aikrof\Hydrator\Components;

use Aikrof\Hydrator\Exceptions\ClassNotFoundException;
use Aikrof\Hydrator\Components\FileManager\NativeFileManager;

/**
 * Class Instance
 *
 * @todo change logic of this class
 */
class Instance implements InstanceInterface
{
    private static $throwError = true;

    /**
     * {@inheritDoc}
     */
    public static function create(string $class): object
    {
        // TO DO (create normal `create instance`)
        switch (self::getCurrentEnvironment()){
            case 'yii':
                return self::createFromYiiContainer($class);
            case 'laravel':
                return self::createFromLaravelContainer($class);
            case 'native':
                return self::createNativeObject($class);
        }
    }

    public static function createIfExist(string $class): ?object
    {
        // TO DO (create normal container exist function)
        switch (self::getCurrentEnvironment()){
            case 'yii':
                return null;
            case 'laravel':
                return null;
            case 'native':
                if (empty(NativeFileManager::getNativeContainer()[$class])){
                    return null;
                }
                return self::createNativeObject($class);
        }
    }

    /**
     * {@inheritDoc}
     */
    private static function createFromYiiContainer(string $class): object
    {

    }

    /**
     * {@inheritDoc}
     */
    private static function createFromLaravelContainer(string $class): object
    {

    }

    /**
     * {@inheritDoc}
     */
    private static function createNativeObject(string $class): object
    {
        $container = NativeFileManager::getNativeContainer();

        if (empty($container[$class]) && \class_exists($class) === false){
            throw new ClassNotFoundException($class);
        }

        $className = !empty($container[$class]) ? $container[$class] : $class;

        return new $className;
    }

    /**
     * {@inheritDoc}
     */
    public static function getCurrentEnvironment(): string
    {
        $yiiEnvironment = \class_exists('\Yii');
        $laravelEnvironment = \class_exists('\App');

        if ($yiiEnvironment || $laravelEnvironment) {
            return $yiiEnvironment ? 'yii' : 'laravel';
        }

        return 'native';
    }
}