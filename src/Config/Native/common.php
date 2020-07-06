<?php

return [
    'container' => [
        // default container setup
        \Aikrof\Hydrator\Interfaces\ServiceHydratorInterface::class => \Aikrof\Hydrator\ServiceHydrator::class,
//        \Aikrof\Hydrator\Interfaces\ReflectionInterface::class => \Aikrof\Hydrator\Core\Reflection::class,

        // cache setup
        \Aikrof\Hydrator\Interfaces\CacheInterface::class => \Aikrof\Hydrator\Components\RedisCache\RedisCache::class,
    ],
];