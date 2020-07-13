<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator\Components\FileManager
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Components\FileManager;

use Aikrof\Hydrator\Exceptions\FileNotFoundException;

/**
 * Class NativeFileManager
 */
class NativeFileManager
{
    /**
     * Path to native config file.
     */
    public const PATH_TO_NATIVE_CONFIG = __DIR__ . '/../../Config/common.php';

    /**
     * Get Native config from config file.
     *
     * @return array|null
     */
    public static function getNativeConfig(): ?array
    {
        $pathToConfig = \realpath(self::PATH_TO_NATIVE_CONFIG );

        if (!$pathToConfig || !is_file($pathToConfig)){
            throw new FileNotFoundException('', $pathToConfig);
        }

        return include $pathToConfig;
    }

    /**
     * Get container from Native config file.
     *
     * @return array|null
     */
    public static function getNativeContainer(): ?array
    {
        $config = self::getNativeConfig();
        
        return \array_key_exists('container', $config) ? $config['container'] : null;
    }
}