<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator\Interfaces
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Interfaces;

/**
 * Interface CacheInterface
 */
interface CacheInterface
{
    /**
     * Set data mappings to cache.
     *
     * @param string $key
     * @param string $value
     */
    public function set(string $key, string $value): void;

    /**
     * Get data mappings from cache.
     *
     * @param string $key
     *
     * @return string|null
     */
    public function get(string $key): ?string;
}