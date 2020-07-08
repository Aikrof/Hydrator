<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator;

use Aikrof\Hydrator\Components\Instance;
use Aikrof\Hydrator\Interfaces\ServiceHydratorInterface;
use Aikrof\Hydrator\Core\ExtractService;
use Aikrof\Hydrator\Core\HydrateService;

/**
 * Class ServiceHydrator
 */
final class ServiceHydrator implements ServiceHydratorInterface
{
    /**
     * @var \Aikrof\Hydrator\Core\ExtractService|null
     */
    private $extractService;

    /**
     * @var \Aikrof\Hydrator\Core\HydrateService|null
     */
    private $hydrateService;

    /**
     * ServiceHydrator constructor.
     *
     * @param \Aikrof\Hydrator\Core\ExtractService|null $extractService
     * @param \Aikrof\Hydrator\Core\HydrateService|null $hydrateService
     */
    public function __construct(ExtractService $extractService = null, HydrateService $hydrateService = null)
    {
        $this->extractService = $extractService ?: Instance::create(ExtractService::class);
        $this->hydrateService = $hydrateService ?: Instance::create(HydrateService::class);
    }

    /**
     * @inheritDoc
     *
     * @param object $entity Object entity.
     * @param array  $exclude define list of fields that should be excluded.
     * @param bool   $hideNullProperties If set `$hideNullProperties` = false null properties will be mapped to array only if:
     *      1. If in field annotation we set null like compound property (string|null).
     *      2. If we haven't any annotation for field.
     *      3. If in field annotation we have `@internal` tag.
     *
     * @return array
     *
     * @throws \Aikrof\Hydrator\Exceptions\HydratorExeption
     */
    public function extract(object $entity, array $exclude, bool $hideNullProperties): array
    {
        return $this->extractService->extract($entity, $exclude, $hideNullProperties);
    }

    /**
     * @inheritDoc
     *
     * @param object|string     $entity
     * @param array             $data
     *
     * @return object
     *
     * @throws \Aikrof\Hydrator\Exceptions\ClassNotFoundException
     * @throws \Aikrof\Hydrator\Exceptions\HydratorExeption
     */
    public function hydrate($entity, array $data): ?object
    {
        if (\is_object($entity)) {
            return $this->hydrateService->hydrateToEntity($entity, $data);
        }

        if (\is_string($entity)) {
            return $this->hydrateService->createEntityAndHydrate($entity, $data);
        }

        return null;
    }
}