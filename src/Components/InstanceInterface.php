<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator\Components
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Components;

/**
 * Interface InstanceInterface
 */
interface InstanceInterface
{
    /**
     * @param string $class
     *
     * @return \Aikrof\Hydrator\Interfaces\ServiceHydratorInterface
     */
    public static function create(string $class): object;

    /**
     * Get current environment (Native, Yii or Laravel), default Native
     * Native - it means that we don't use any container, just `new` for creating objects.
     *
     * @return string
     */
    public static function getCurrentEnvironment(): string;
}