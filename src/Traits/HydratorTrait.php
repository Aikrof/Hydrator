<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator\Traits
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types=1);

namespace Aikrof\Hydrator\Traits;

use Aikrof\Hydrator\Entity\EntityInterface;
use Aikrof\Hydrator\Hydrator;

/**
 * Trait HydratorTrait
 */
trait HydratorTrait
{
    /**
     * Extract data from this object to array
     *
     * @param array $exclude define list of fields that should be excluded.
     * @param bool  $hideNullProperties If set `$hideNullProperties` = true, null properties will be mapped to array only if:
     *        1. In field annotation we set null like compound property (string|null).
     *        2. We haven't any annotation for field.
     *        3. In field annotation we have `@internal` tag.
     *
     * @return array
     */
    public function extractData(array $exclude = [], bool $hideNullProperties = false): array
    {
        return Hydrator::extract($this, $exclude, $hideNullProperties);
    }

    /**
     * Hydrate data from array to this object
     *
     * @param array $data
     */
    public function hydrateData(array $data): void
    {
        Hydrator::hydrate($this, $data);
    }
}