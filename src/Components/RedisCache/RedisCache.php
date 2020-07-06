<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator\Components\RedisCache
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Components\RedisCache;

use Redis;
use Aikrof\Hydrator\Interfaces\CacheInterface;
use Aikrof\Hydrator\Components\Instance;

/**
 * Class RedisCache
 */
class RedisCache implements CacheInterface
{
    /**
     * @var Redis|null
     */
    private $redis;

    public function __construct(Redis $redis = null)
    {
        $this->redis = $redis ?: Instance::create(Redis::class);
        try {
            @$this->redis->connect('localhost', 6379);
        } catch (\Exception $e) {
        }
    }

    public function set(string $key, string $value): void
    {
        $this->redis->hSet('mapper', $key, $value);
    }

    public function get(string $key): ?string
    {
        $data = $this->redis->hGet('mapper', $key);

        return ($data === false ? null : $data);
    }
}