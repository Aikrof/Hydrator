<?php

return [
    'container' => [
        \Aikrof\Hydrator\Interfaces\ServiceHydratorInterface::class => \Aikrof\Hydrator\Core\ServiceHydrator::class,
        \Aikrof\Hydrator\Interfaces\ReflectionInterface::class => \Aikrof\Hydrator\Core\Reflection::class,
    ]
];