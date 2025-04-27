<?php

namespace SoureCode\Bundle\Loupe\Factory;

/**
 * @template T
 */
interface DocumentFactoryInterface
{
    /**
     * @psalm-param T $object
     */
    public function create(object $object, ?string $locale = null): array;
}
