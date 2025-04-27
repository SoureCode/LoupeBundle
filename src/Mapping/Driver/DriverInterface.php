<?php

namespace SoureCode\Bundle\Loupe\Mapping\Driver;

use SoureCode\Bundle\Loupe\Mapping\ClassMetadata;

interface DriverInterface
{
    /**
     * @param class-string $className
     */
    public function load(string $className): ?ClassMetadata;
}
