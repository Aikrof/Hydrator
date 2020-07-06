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

/**
 * Class Instance
 *
 * @todo change logic of this class
 */
class Instance
{
    private static $throwError = true;


    public static function create(string $class)
    {
        // TO DO (create normal `create instance`)
        switch (self::getCurrentEnvironment()) {
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


    private static function createFromYiiContainer(string $class): object
    {

    }


    private static function createFromLaravelContainer(string $class): object
    {

    }


    private static function createNativeObject(string $class)
    {
        $container = NativeFileManager::getNativeContainer();

        if (empty($container[$class]) && !\class_exists($class)){
            throw new ClassNotFoundException($class);
        }

        $className = !empty($container[$class]) ? $container[$class] : $class;

        return new $className;
    }

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